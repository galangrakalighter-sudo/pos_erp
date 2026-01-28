<?php

namespace App\Http\Controllers;

use App\Models\ExternalItem;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExternalItemController extends Controller
{
    /**
     * Display a listing of external items for the authenticated client.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'client') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get client ID from user's client_id
        $client = Client::where('client_id', $user->client_id)->first();
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query = ExternalItem::where('client_id', $client->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $externalItems = $query->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

        return response()->json($externalItems);
    }

    /**
     * Store a newly created external item.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'client') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get client ID from user's client_id
        $client = Client::where('client_id', $user->client_id)->first();
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        $request->validate([
            'item_name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $subtotal = $request->quantity * $request->unit_price;

        $externalItem = ExternalItem::create([
            'client_id' => $client->id,
            'item_name' => $request->item_name,
            'sku' => $request->sku,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'subtotal' => $subtotal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item luar berhasil disimpan!',
            'data' => $externalItem
        ], 201);
    }

    /**
     * Display the specified external item.
     */
    public function show(string $id): JsonResponse
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'client') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get client ID from user's client_id
        $client = Client::where('client_id', $user->client_id)->first();
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        $externalItem = ExternalItem::where('id', $id)
            ->where('client_id', $client->id)
            ->first();

        if (!$externalItem) {
            return response()->json(['error' => 'Item luar tidak ditemukan'], 404);
        }

        return response()->json($externalItem);
    }

    /**
     * Update the specified external item.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'client') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get client ID from user's client_id
        $client = Client::where('client_id', $user->client_id)->first();
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        $externalItem = ExternalItem::where('id', $id)
            ->where('client_id', $client->id)
            ->first();

        if (!$externalItem) {
            return response()->json(['error' => 'Item luar tidak ditemukan'], 404);
        }

        $request->validate([
            'item_name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $subtotal = $request->quantity * $request->unit_price;

        $externalItem->update([
            'item_name' => $request->item_name,
            'sku' => $request->sku,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'subtotal' => $subtotal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item luar berhasil diperbarui!',
            'data' => $externalItem
        ]);
    }

    /**
     * Remove the specified external item.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'client') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get client ID from user's client_id
        $client = Client::where('client_id', $user->client_id)->first();
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        $externalItem = ExternalItem::where('id', $id)
            ->where('client_id', $client->id)
            ->first();

        if (!$externalItem) {
            return response()->json(['error' => 'Item luar tidak ditemukan'], 404);
        }

        $externalItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item luar berhasil dihapus!'
        ]);
    }
}