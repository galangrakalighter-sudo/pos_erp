<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Jika expects JSON, return data sales + items
        if ($request->expectsJson() || $request->wantsJson()) {
            $sales = Sale::with('items')->orderBy('created_at', 'desc')->get();
            return response()->json($sales);
        }
        // Default: return view
        $sales = Sale::orderBy('created_at', 'desc')->get();
        return view('admin.sales', ['sales' => $sales]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not used, we are using a modal.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log data yang diterima untuk debugging
        \Log::info('Sales Store Request Data', [
            'all_data' => $request->all(),
            'headers' => $request->headers->all(),
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method()
        ]);

        $data = $request->validate([
            'nama_pemesan' => 'required',
            'id_pesanan' => 'required',
            'nama_sales' => 'nullable|string|max:255',
            'status' => 'required',
            'periode' => 'required|date',
            'jenis_transaksi' => 'required',
            'telepon' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
            'diskon_tipe' => 'nullable',
            'diskon_nilai' => 'nullable|numeric',
            'diskon_ball_tipe' => 'nullable|in:rupiah,persen',
            'diskon_ball_nilai' => 'nullable|numeric|min:0',
            'nama_ekspedisi' => 'nullable|string',
            'ongkir' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:stock_items,id',
            'items.*.selectedQuantity' => 'required|integer|min:1',
            'items.*.harga' => 'required|integer|min:0',
        ]);

        try {
            // Hitung subtotal
        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += $item['harga'] * $item['selectedQuantity'];
        }

        // Hitung diskon reguler
        $diskon = 0;
        if ($data['diskon_tipe'] === 'rupiah') {
            $diskon = min($data['diskon_nilai'], $subtotal);
        } elseif ($data['diskon_tipe'] === 'persen') {
            $diskon = ($subtotal * $data['diskon_nilai']) / 100;
        }
        
        // Hitung diskon ball
        $diskonBall = 0;
        if ($data['diskon_ball_tipe'] === 'rupiah') {
            $diskonBall = min($data['diskon_ball_nilai'], $subtotal);
        } elseif ($data['diskon_ball_tipe'] === 'persen') {
            $diskonBall = ($subtotal * $data['diskon_ball_nilai']) / 100;
        }
        
        // Hitung ongkir
        $ongkir = $data['ongkir'] ?? 0;
        
        // Total setelah semua diskon dan ongkir
        $total = $subtotal - $diskon - $diskonBall + $ongkir;

        // Simpan sales
        $sale = Sale::create([
            'nama_pemesan' => $data['nama_pemesan'],
            'id_pesanan' => $data['id_pesanan'],
            'nama_sales' => $data['nama_sales'] ?? null,
            'status' => $data['status'],
            'periode' => $data['periode'],
            'jenis_transaksi' => $data['jenis_transaksi'],
            'telepon' => $data['telepon'],
            'alamat' => $data['alamat'],
            'diskon_tipe' => $data['diskon_tipe'],
            'diskon_nilai' => $data['diskon_nilai'],
            'diskon_ball_tipe' => $data['diskon_ball_tipe'],
            'diskon_ball_nilai' => $data['diskon_ball_nilai'],
            'nama_ekspedisi' => $data['nama_ekspedisi'],
            'ongkir' => $ongkir,
            'notes' => $data['notes'] ?? '',
            'total_quantity' => array_sum(array_column($data['items'], 'selectedQuantity')),
            'total_diskon' => $diskon,
            'total_diskon_ball' => $diskonBall,
            'total_harga' => $total,
        ]);

        // Simpan item ke pivot dan kurangi stok dengan histori
        foreach ($data['items'] as $item) {
            $sale->items()->attach($item['id'], [
                'quantity' => $item['selectedQuantity'],
                'harga' => $item['harga'],
                'subtotal' => $item['harga'] * $item['selectedQuantity'],
            ]);

            // Kurangi stok dan catat histori
            $stock = \App\Models\StockItem::find($item['id']);
            $stock->decreaseStockForSale(
                $item['selectedQuantity'], 
                $sale->id, 
                $data['nama_pemesan'], 
                $data['id_pesanan']
            );
        }

        return response()->json(['success' => true, 'sale' => $sale->load('items')]);
        
        } catch (\Exception $e) {
            \Log::error('Sales Store Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sale = Sale::with('items')->findOrFail($id);
        
        $data = $request->validate([
            'nama_pemesan' => 'nullable|string',
            'id_pesanan' => 'nullable|string',
            'nama_sales' => 'nullable|string',
            'status' => 'nullable|string',
            'periode' => 'nullable|date',
            'jenis_transaksi' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'diskon_tipe' => 'nullable|string',
            'diskon_nilai' => 'nullable|numeric',
            'items' => 'nullable|array|min:1',
            'items.*.id' => 'required_with:items|exists:stock_items,id',
            'items.*.selectedQuantity' => 'required_with:items|integer|min:1',
            'items.*.harga' => 'required_with:items|integer|min:0',
        ]);

        // Kembalikan stok lama jika ada perubahan items
        if (isset($data['items'])) {
            foreach ($sale->items as $oldItem) {
                $stock = \App\Models\StockItem::find($oldItem->id);
                $stock->increaseStockForRefund(
                    $oldItem->pivot->quantity, 
                    $sale->id, 
                    $sale->id_pesanan,
                    "Stok dikembalikan karena update penjualan {$sale->id_pesanan}"
                );
            }
            $sale->items()->detach();
        }

        // Update field
        foreach (['nama_pemesan', 'id_pesanan', 'nama_sales', 'status', 'periode', 'jenis_transaksi', 'telepon', 'alamat', 'diskon_tipe', 'diskon_nilai'] as $field) {
            if (isset($data[$field])) {
                $sale->$field = $data[$field];
            }
        }

        // Hitung ulang total jika items diupdate
        if (isset($data['items'])) {
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += $item['harga'] * $item['selectedQuantity'];
            }

            $diskon = 0;
            if ($sale->diskon_tipe === 'rupiah') {
                $diskon = min($sale->diskon_nilai, $subtotal);
            } elseif ($sale->diskon_tipe === 'persen') {
                $diskon = ($subtotal * $sale->diskon_nilai) / 100;
            }
            $total = $subtotal - $diskon;

            $sale->total_quantity = array_sum(array_column($data['items'], 'selectedQuantity'));
            $sale->total_diskon = $diskon;
            $sale->total_harga = $total;

            // Simpan item baru ke pivot dan kurangi stok
            foreach ($data['items'] as $item) {
                $sale->items()->attach($item['id'], [
                    'quantity' => $item['selectedQuantity'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['harga'] * $item['selectedQuantity'],
                ]);
                
                $stock = \App\Models\StockItem::find($item['id']);
                $stock->decreaseStockForSale(
                    $item['selectedQuantity'], 
                    $sale->id, 
                    $sale->nama_pemesan, 
                    $sale->id_pesanan
                );
            }
        }

        $sale->save();
        return response()->json(['message' => 'Data sales berhasil diupdate', 'data' => $sale->load('items')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $sale = Sale::with('items')->findOrFail($id);
            
            // Hapus relasi item (detach pivot)
            $sale->items()->detach();
            
            // Hapus sale
            $sale->delete();
            
            return response()->json(['success' => true, 'message' => 'Data penjualan berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}
