<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    /**
     * Get all purchases for admin.
     */
    public function index(): JsonResponse
    {
        $purchases = Purchase::with(['admin', 'items'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'purchase_number' => $purchase->purchase_number,
                    'supplier_name' => $purchase->supplier_name,
                    'supplier_contact' => $purchase->supplier_contact,
                    'invoice_number' => $purchase->invoice_number,
                    'total_amount' => $purchase->total_amount,
                    'status' => $purchase->status,
                    'payment_status' => $purchase->payment_status,
                    'purchase_date' => $purchase->purchase_date,
                    'due_date' => $purchase->due_date,
                    'notes' => $purchase->notes,
                    'admin_name' => $purchase->admin->name,
                    'created_at' => $purchase->created_at,
                    'items' => $purchase->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'item_name' => $item->item_name,
                            'item_description' => $item->item_description,
                            'sku' => $item->sku,
                            'category' => $item->category,
                            'quantity' => $item->quantity,
                            'unit' => $item->unit,
                            'unit_price' => $item->unit_price,
                            'subtotal' => $item->subtotal,
                        ];
                    }),
                ];
            });

        return response()->json($purchases);
    }

    /**
     * Store a new purchase.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'purchase_number' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'supplier_contact' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:purchase_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.sku' => 'nullable|string|max:255',
            'items.*.category' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $purchase = Purchase::create([
                'admin_id' => Auth::id(),
                'purchase_number' => $request->purchase_number,
                'supplier_name' => $request->supplier_name,
                'supplier_contact' => $request->supplier_contact,
                'invoice_number' => $request->invoice_number,
                'purchase_date' => $request->purchase_date,
                'due_date' => $request->due_date,
                'notes' => $request->notes,
                'total_amount' => $request->total_amount,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            foreach ($request->items as $itemData) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_name' => $itemData['item_name'],
                    'item_description' => $itemData['item_description'] ?? null,
                    'sku' => $itemData['sku'] ?? null,
                    'category' => $itemData['category'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'unit_price' => $itemData['unit_price'],
                    'subtotal' => $itemData['subtotal'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Purchase created successfully',
                'purchase' => $purchase->load('items')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create purchase'], 500);
        }
    }

    /**
     * Approve a purchase.
     */
    public function approve($id): JsonResponse
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->status !== 'pending') {
            return response()->json(['error' => 'Only pending purchases can be approved'], 400);
        }

        // Approved: belum paid. Payment akan menjadi paid saat completed
        $purchase->update(['status' => 'approved']);

        return response()->json(['message' => 'Purchase approved successfully']);
    }

    /**
     * Reject a purchase.
     */
    public function reject($id): JsonResponse
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->status !== 'pending') {
            return response()->json(['error' => 'Only pending purchases can be rejected'], 400);
        }

        $purchase->update(['status' => 'rejected']);

        return response()->json(['message' => 'Purchase rejected successfully']);
    }

    /**
     * Mark purchase as completed.
     */
    public function complete($id): JsonResponse
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->status !== 'approved') {
            return response()->json(['error' => 'Only approved purchases can be completed'], 400);
        }

        $purchase->update(['status' => 'completed', 'payment_status' => 'paid']);

        return response()->json(['message' => 'Purchase marked as completed successfully']);
    }

    /**
     * Mark purchase as returned (paid automatically).
     */
    public function markReturned($id): JsonResponse
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->status === 'returned') {
            return response()->json(['message' => 'Already returned']);
        }

        $purchase->update([
            'status' => 'returned',
            'payment_status' => 'paid'
        ]);

        return response()->json(['message' => 'Purchase marked as returned successfully']);
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_status' => 'required|in:unpaid,partial,paid',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $purchase = Purchase::findOrFail($id);
        $purchase->update(['payment_status' => $request->payment_status]);

        return response()->json(['message' => 'Payment status updated successfully']);
    }
}
