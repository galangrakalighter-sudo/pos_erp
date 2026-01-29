<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    /**
     * Get purchase orders for the authenticated client.
     */
    public function index(): JsonResponse
    {
        $purchaseOrders = PurchaseOrder::with(['items.stockItem'])
            ->where('client_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($po) {
                return [
                    'id' => $po->id,
                    'po_number' => $po->po_number,
                    'total_amount' => $po->total_amount,
                    'status' => $po->status,
                    'payment_status' => $po->payment_status,
                    'created_at' => $po->created_at,
                    'items' => $po->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'item_name' => $item->item_name,
                            'sku' => $item->sku,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'subtotal' => $item->subtotal,
                            'item_type' => $item->item_type,
                        ];
                    }),
                ];
            });

        return response()->json($purchaseOrders);
    }

    /**
     * Get a specific purchase order for the authenticated client.
     */
    public function show($id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::with(['items.stockItem', 'client'])
            ->where('id', $id)
            ->where('client_id', Auth::id())
            ->first();

        if (!$purchaseOrder) {
            return response()->json(['error' => 'Purchase order not found'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $purchaseOrder->id,
                'po_number' => $purchaseOrder->po_number,
                'total_amount' => $purchaseOrder->total_amount,
                'status' => $purchaseOrder->status,
                'payment_status' => $purchaseOrder->payment_status,
                'created_at' => $purchaseOrder->created_at,
                'client_name' => $purchaseOrder->client->name,
                'client_email' => $purchaseOrder->client->email,
                'client_phone' => $purchaseOrder->client->telepon ?? null,
                'items' => $purchaseOrder->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'item_name' => $item->item_name,
                        'sku' => $item->sku,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->subtotal,
                        'item_type' => $item->item_type,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Store a new purchase order.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'po_number' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.item_type' => 'required|in:stock,external',
            'items.*.stock_item_id' => 'nullable|exists:stock_items,id',
            'items.*.sku' => 'nullable|string|max:255',
            'total_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $purchaseOrder = PurchaseOrder::create([
                'client_id' => Auth::id(),
                'po_number' => $request->po_number,
                'total_amount' => $request->total_amount,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            foreach ($request->items as $itemData) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'stock_item_id' => $itemData['item_type'] === 'stock' ? $itemData['stock_item_id'] : null,
                    'item_name' => $itemData['item_name'],
                    'sku' => $itemData['sku'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'subtotal' => $itemData['subtotal'],
                    'item_type' => $itemData['item_type'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Purchase order created successfully',
                'purchase_order' => $purchaseOrder->load('items')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create purchase order'], 500);
        }
    }

    /**
     * Cancel a purchase order.
     */
    public function cancel($id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::where('client_id', Auth::id())->findOrFail($id);

        if ($purchaseOrder->status !== 'pending') {
            return response()->json(['error' => 'Only pending purchase orders can be cancelled'], 400);
        }

        $purchaseOrder->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Purchase order cancelled successfully']);
    }

    /**
     * Get all purchase orders for admin.
     */
    public function adminIndex(): JsonResponse
    {
        $purchaseOrders = PurchaseOrder::with(['client', 'items.stockItem'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($po) {
                return [
                    'id' => $po->id,
                    'po_number' => $po->po_number,
                    'client_name' => $po->client->name,
                    'client_email' => $po->client->email,
                    'client_phone' => $po->client->telepon ?? null,
                    'total_amount' => $po->total_amount,
                    'status' => $po->status,
                    'payment_status' => $po->payment_status,
                    'created_at' => $po->created_at,
                    'items' => $po->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'item_name' => $item->item_name,
                            'sku' => $item->sku,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'subtotal' => $item->subtotal,
                            'item_type' => $item->item_type,
                        ];
                    }),
                ];
            });

        return response()->json($purchaseOrders);
    }

    /**
     * Approve a purchase order.
     */
    public function approve($id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->status !== 'pending') {
            return response()->json(['error' => 'Only pending purchase orders can be approved'], 400);
        }

        $purchaseOrder->update(['status' => 'approved']);

        return response()->json(['message' => 'Purchase order approved successfully']);
    }

    /**
     * Mark purchase order as paid.
     */
    public function markAsPaid($id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->status !== 'approved') {
            return response()->json(['error' => 'Only approved purchase orders can be marked as paid'], 400);
        }

        if ($purchaseOrder->payment_status === 'paid') {
            return response()->json(['error' => 'Purchase order is already paid'], 400);
        }

        $purchaseOrder->update(['payment_status' => 'paid']);

        return response()->json(['message' => 'Purchase order marked as paid successfully']);
    }

    /**
     * Mark purchase order as received.
     */
public function markAsReceived($id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->status !== 'approved') {
            return response()->json(['error' => 'Only approved purchase orders can be marked as received'], 400);
        }

        if ($purchaseOrder->payment_status !== 'paid') {
            return response()->json(['error' => 'Purchase order must be paid before marking as received'], 400);
        }

        $purchaseOrder->update(['status' => 'received']);

        // Ambil Data Dari Table Purchase Order Items
        $purchaseOrderItems = PurchaseOrderItem::where('purchase_order_id', $purchaseOrder->id)->get();
        foreach($purchaseOrderItems as $po_items){
            StockItem::where('sku', $po_items->sku)
            ->decrement('tersedia', $po_items->quantity);
        }

        return response()->json(['message' => 'Purchase order marked as received successfully']);
    }

    /**
     * Reject a purchase order (admin). Hidden from admin list, client sees status 'rejected'.
     */
    public function reject($id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if (!in_array($purchaseOrder->status, ['pending', 'approved'])) {
            return response()->json(['error' => 'Only pending/approved purchase orders can be rejected'], 400);
        }

        $purchaseOrder->update([
            'status' => 'rejected',
            'payment_status' => 'unpaid',
        ]);

        return response()->json(['message' => 'Purchase order rejected successfully']);
    }

    /**
     * Permanently delete a purchase order by client if not processed.
     */
    public function destroy($id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::where('client_id', Auth::id())->with('items')->findOrFail($id);

        if (in_array($purchaseOrder->status, ['approved', 'received'])) {
            return response()->json(['error' => 'Cannot delete approved/received purchase orders'], 400);
        }

        DB::transaction(function () use ($purchaseOrder) {
            PurchaseOrderItem::where('purchase_order_id', $purchaseOrder->id)->delete();
            $purchaseOrder->delete();
        });

        return response()->json(['message' => 'Purchase order deleted successfully']);
    }

    /**
     * Permanently delete a purchase order by admin. Allowed only when status is rejected.
     */
    public function adminDestroy($id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::with('items')->findOrFail($id);

        if ($purchaseOrder->status !== 'rejected') {
            return response()->json(['error' => 'Only rejected purchase orders can be deleted by admin'], 400);
        }

        DB::transaction(function () use ($purchaseOrder) {
            PurchaseOrderItem::where('purchase_order_id', $purchaseOrder->id)->delete();
            $purchaseOrder->delete();
        });

        return response()->json(['message' => 'Purchase order deleted successfully']);
    }
}
