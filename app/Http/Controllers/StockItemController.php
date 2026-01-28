<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockItemHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StockItemController extends Controller
{
    public function list()
    {
        $items = StockItem::orderByDesc('id')->get()->map(function (StockItem $i) {
            return [
                'id' => $i->id,
                'nama' => $i->nama,
                'sku' => $i->sku,
                'lokasi' => $i->lokasi,
                'tersedia' => (int) $i->tersedia,
                'harga' => (int) $i->harga,
                'diperbaharui' => optional($i->diperbaharui)->toDateString(),
                'gambar' => $i->gambar ? ('storage/' . ltrim($i->gambar, '/')) : 'images/gambar.png',
                'created_at' => $i->created_at?->toISOString(),
                'updated_at' => $i->updated_at?->toISOString(),
            ];
        });

        return response()->json($items);
    }

    public function history(int $id)
    {
        $histories = StockItemHistory::where('stock_item_id', $id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (StockItemHistory $h) {
                return [
                    'id' => $h->id,
                    'timestamp' => $h->created_at?->toISOString(),
                    'action' => $h->action,
                    'changes' => $h->changes,
                    'user' => $h->user ?? 'Admin',
                ];
            });
        return response()->json($histories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'sku' => 'required|string|max:50',
            'lokasi' => 'required|string|max:100',
            'tersedia' => 'required|integer|min:0',
            'harga' => 'required|integer|min:0',
            'foto' => 'nullable|string',
        ]);

        $path = null;
        if (!empty($validated['foto']) && str_starts_with($validated['foto'], 'data:image')) {
            $name = 'item_' . time() . '_' . Str::random(6) . '.png';
            $rel = 'item_photos/' . $name;
            $data = explode(',', $validated['foto']);
            Storage::disk('public')->put($rel, base64_decode(end($data)));
            $path = $rel;
        }

        $today = now()->toDateString();

        $item = StockItem::create([
            'nama' => $validated['nama'],
            'sku' => $validated['sku'],
            'kondisi' => 'Baru',
            'lokasi' => $validated['lokasi'],
            'tersedia' => $validated['tersedia'],
            'disimpan' => 0,
            'harga' => $validated['harga'],
            'diperbaharui' => $today,
            'gambar' => $path,
        ]);

        // log history create
        $this->logHistory($item, 'Item Baru Dibuat', [
            'nama' => $item->nama,
            'sku' => $item->sku,
            'lokasi' => $item->lokasi,
            'tersedia' => $item->tersedia,
            'harga' => $item->harga,
            'gambar' => $item->gambar ? 'uploaded' : 'default'
        ]);

        return response()->json($this->serializeItem($item), 201);
    }

    public function update(Request $request, int $id)
    {
        // Pastikan item dengan ID ini benar-benar ada (tidak membuat item baru)
        $item = StockItem::find($id);
        if (!$item) {
            return response()->json([
                'message' => 'Item tidak ditemukan',
                'error' => 'Item dengan ID ' . $id . ' tidak ditemukan. Pastikan ID valid dan item sudah ada di database.'
            ], 404);
        }

        // Simpan nilai sebelum update untuk history
        $before = $item->only(['nama','sku','lokasi','tersedia','harga','gambar']);
        $beforeTersedia = (int) ($item->tersedia ?? 0);

        $validated = $request->validate([
            'nama' => 'nullable|string|max:150',
            'sku' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:100',
            'tersedia' => 'nullable|integer|min:0',
            'harga' => 'nullable|integer|min:0',
            'foto' => 'nullable|string',
        ]);

        // Update hanya field yang dikirim (tidak membuat item baru)
        foreach (['nama','sku','lokasi','tersedia','harga'] as $key) {
            if (array_key_exists($key, $validated)) {
                // Pastikan tersedia tidak menjadi 0 jika tidak dikirim
                if ($key === 'tersedia' && $validated[$key] === null) {
                    continue; // Skip jika null
                }
                $item->$key = $validated[$key];
            }
        }

        if (isset($validated['foto']) && str_starts_with($validated['foto'], 'data:image')) {
            $name = 'item_' . time() . '_' . Str::random(6) . '.png';
            $rel = 'item_photos/' . $name;
            $data = explode(',', $validated['foto']);
            Storage::disk('public')->put($rel, base64_decode(end($data)));
            $item->gambar = $rel;
        }

        $item->diperbaharui = now()->toDateString();
        $item->save();

        // Refresh item untuk mendapatkan nilai terbaru
        $item->refresh();

        // diff changes untuk history
        $after = $item->only(['nama','sku','lokasi','tersedia','harga','gambar']);
        $changes = [];
        foreach ($after as $k => $v) {
            $beforeVal = $before[$k] ?? null;
            if ($v != $beforeVal) {
                if ($k === 'tersedia') {
                    $afterTersedia = (int) $v;
                    $selisih = $afterTersedia - $beforeTersedia;
                    $changes['stok'] = [
                        'stok_lama' => $beforeTersedia,
                        'stok_baru' => $afterTersedia,
                        'perubahan' => $selisih > 0 ? "+{$selisih}" : "{$selisih}",
                        'stok_total' => $afterTersedia,
                    ];
                    // Format untuk history yang lebih jelas
                    if ($selisih > 0) {
                        $changes['stok']['catatan'] = "Ditambahkan: +{$selisih} (dari {$beforeTersedia} menjadi {$afterTersedia})";
                    } elseif ($selisih < 0) {
                        $changes['stok']['catatan'] = "Dikurangi: {$selisih} (dari {$beforeTersedia} menjadi {$afterTersedia})";
                    } else {
                        $changes['stok']['catatan'] = "Stok diubah dari {$beforeTersedia} menjadi {$afterTersedia}";
                    }
                } elseif ($k === 'gambar') {
                    $changes['gambar'] = ['dari' => $beforeVal ? 'uploaded' : 'default', 'ke' => $v ? 'uploaded' : 'default'];
                } else {
                    $changes[$k] = ['dari' => $beforeVal, 'ke' => $v];
                }
            }
        }
        if (!empty($changes)) {
            $this->logHistory($item, 'Item Diperbaharui', $changes);
        }

        return response()->json($this->serializeItem($item));
    }

    public function destroy(int $id)
    {
        $item = StockItem::find($id);
        if (!$item) return response()->json(['message' => 'Item tidak ditemukan'], 404);

        // Hapus semua history terkait
        $item->histories()->delete();

        // Hapus gambar jika ada
        if ($item->gambar && Storage::disk('public')->exists($item->gambar)) {
            Storage::disk('public')->delete($item->gambar);
        }
        $item->delete();
        return response()->json(['message' => 'Item dihapus']);
    }

    private function serializeItem(StockItem $i): array
    {
        return [
            'id' => $i->id,
            'nama' => $i->nama,
            'sku' => $i->sku,
            'lokasi' => $i->lokasi,
            'tersedia' => (int) $i->tersedia,
            'harga' => (int) $i->harga,
            'diperbaharui' => optional($i->diperbaharui)->toDateString(),
            'gambar' => $i->gambar ? ('storage/' . ltrim($i->gambar, '/')) : 'images/gambar.png',
            'created_at' => $i->created_at?->toISOString(),
            'updated_at' => $i->updated_at?->toISOString(),
        ];
    }

    private function logHistory(StockItem $item, string $action, array|string $changes): void
    {
        StockItemHistory::create([
            'stock_item_id' => $item->id,
            'nama_item' => $item->nama,
            'tersedia' => $item->tersedia,
            'action' => $action,
            'changes' => is_array($changes) ? $changes : ['catatan' => (string) $changes],
            'user' => optional(auth()->user())->name ?? 'Admin',
        ]);
    }
} 