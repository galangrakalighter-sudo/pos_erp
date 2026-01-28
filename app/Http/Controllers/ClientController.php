<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientItem;
use App\Models\ClientHistory;
use App\Models\StockItem;
use App\Models\StockItemHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClientStockItem;

class ClientController extends Controller
{
    public function list()
    {
        $clients = Client::with(['items.stockItem'])->orderByDesc('id')->get()->map(function (Client $client) {
            return $this->serializeClient($client);
        });

        return response()->json($clients);
    }

    public function show($id)
    {
        $client = Client::where('client_id', $id)->with(['items.stockItem'])->first();
        
        if (!$client) {
            return response()->json(['message' => 'Client tidak ditemukan'], 404);
        }

        return response()->json($this->serializeClient($client));
    }

    public function history($id)
    {
        $client = Client::where('client_id', $id)->first();
        if (!$client) {
            return response()->json(['message' => 'Client tidak ditemukan'], 404);
        }

        $histories = $client->histories()
            ->orderByDesc('created_at')
            ->get()
            ->map(function (ClientHistory $h) {
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
            'client_id' => 'required|string|max:50',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:30',
            'nama_sales' => 'nullable|string|max:100',
            'tanggal_bergabung' => 'required|date',
            // Tambahan seperti sales
            'diskon_tipe' => 'nullable|in:rupiah,persen',
            'diskon_nilai' => 'nullable|numeric|min:0',
            'diskon_ball_tipe' => 'nullable|in:rupiah,persen',
            'diskon_ball_nilai' => 'nullable|numeric|min:0',
            'nama_ekspedisi' => 'nullable|string|max:150',
            'ongkir' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.stock_item_id' => 'required|integer|exists:stock_items,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $today = now()->toDateString();

            // Cek apakah client dengan ID yang sama sudah ada
            $existingClient = Client::where('client_id', $validated['client_id'])->first();

            if ($existingClient) {
                // Update client yang sudah ada
                $existingClient->update([
                    'nama' => $validated['nama'],
                    'alamat' => $validated['alamat'],
                    'telepon' => $validated['telepon'],
                    'nama_sales' => $validated['nama_sales'] ?? null,
                    'diskon_tipe' => $validated['diskon_tipe'] ?? null,
                    'diskon_nilai' => $validated['diskon_nilai'] ?? 0,
                    'diskon_ball_tipe' => $validated['diskon_ball_tipe'] ?? null,
                    'diskon_ball_nilai' => $validated['diskon_ball_nilai'] ?? 0,
                    'nama_ekspedisi' => $validated['nama_ekspedisi'] ?? null,
                    'ongkir' => $validated['ongkir'] ?? 0,
                    'notes' => $validated['notes'] ?? null,
                    'diperbaharui' => $today,
                ]);

                // Proses items dan update stok
                foreach ($validated['items'] as $itemData) {
                    $stockItem = StockItem::find($itemData['stock_item_id']);
                    
                    if (!$stockItem) {
                        throw new \Exception('Stock item tidak ditemukan');
                    }

                    $beforeStock = $stockItem->tersedia;
                    // Izinkan minus untuk preorder
                    $stockItem->tersedia = $stockItem->tersedia - $itemData['jumlah'];
                    $stockItem->diperbaharui = $today;
                    $stockItem->save();

                    // Log history stok berkurang karena pembelian client
                    $action = 'Stok Berkurang (Pembelian Client)';
                    $changes = [
                        'stok' => [
                            'stok_lama' => (int) $beforeStock,
                            'stok_baru' => - (int) $itemData['jumlah'],
                            'stok_total' => (int) $stockItem->tersedia,
                        ],
                        'catatan' => 'Dikurangi ' . (int) $itemData['jumlah'] . ' untuk client ' . $existingClient->nama . ' (' . $existingClient->client_id . ')',
                    ];
                    if ($beforeStock < $itemData['jumlah']) {
                        $changes['preorder'] = true;
                        $changes['keterangan'] = 'Preorder (hutang sampai stok diisi)';
                        $action = 'Preorder - Stok Negatif (Pembelian Client)';
                    }
                    $this->logStockHistory($stockItem, $action, $changes);

                    // Cek apakah item sudah ada di client
                    $existingItem = ClientItem::where('client_id', $existingClient->id)
                        ->where('stock_item_id', $itemData['stock_item_id'])
                        ->first();

                    if ($existingItem) {
                        // Update jumlah jika item sudah ada
                        $existingItem->update([
                            'jumlah' => $existingItem->jumlah + $itemData['jumlah'],
                            'total_harga' => $stockItem->harga * ($existingItem->jumlah + $itemData['jumlah']),
                        ]);
                    } else {
                        // Buat client item baru
                        ClientItem::create([
                            'client_id' => $existingClient->id,
                            'stock_item_id' => $itemData['stock_item_id'],
                            'jumlah' => $itemData['jumlah'],
                            'harga_satuan' => $stockItem->harga,
                            'total_harga' => $stockItem->harga * $itemData['jumlah'],
                        ]);
                    }

                    // Pada setiap proses items (baik create maupun update), tambahkan update ke ClientStockItem
                    // Bagian store (client baru atau update existing)
                    // Setelah ClientItem::create atau update, tambahkan kode berikut:
                    $existingStock = ClientStockItem::where('client_id', $existingClient->id)
                        ->where('nama', $stockItem->nama)
                        ->where('sku', $stockItem->sku)
                        ->first();
                    if ($existingStock) {
                        $existingStock->tersedia += $itemData['jumlah'];
                        $existingStock->harga = $stockItem->harga;
                        $existingStock->diperbaharui = $today;
                        $existingStock->save();
                    } else {
                        ClientStockItem::create([
                            'client_id' => $existingClient->id,
                            'nama' => $stockItem->nama,
                            'sku' => $stockItem->sku,
                            'kategori' => $stockItem->kategori ?? 'Umum',
                            'tersedia' => $itemData['jumlah'],
                            'harga' => $stockItem->harga,
                            'diperbaharui' => $today,
                        ]);
                    }
                }

                // Log history update
                $this->logHistory($existingClient, 'Stok Ditambahkan', [
                    'nama' => $existingClient->nama,
                    'client_id' => $existingClient->client_id,
                    'alamat' => $existingClient->alamat,
                    'telepon' => $existingClient->telepon,
                    'nama_sales' => $validated['nama_sales'] ?? null,
                    'items' => $validated['items'],
                    'diskon_tipe' => $validated['diskon_tipe'] ?? null,
                    'diskon_nilai' => $validated['diskon_nilai'] ?? 0,
                    'diskon_ball_tipe' => $validated['diskon_ball_tipe'] ?? null,
                    'diskon_ball_nilai' => $validated['diskon_ball_nilai'] ?? 0,
                    'nama_ekspedisi' => $validated['nama_ekspedisi'] ?? null,
                    'ongkir' => $validated['ongkir'] ?? 0,
                    'notes' => $validated['notes'] ?? null,
                ]);

                DB::commit();

                // Return client dengan items
                $existingClient->load(['items.stockItem']);
                return response()->json($this->serializeClient($existingClient), 200);

            } else {
                // Buat client baru
                $client = Client::create([
                    'nama' => $validated['nama'],
                    'client_id' => $validated['client_id'],
                    'alamat' => $validated['alamat'],
                    'telepon' => $validated['telepon'],
                    'nama_sales' => $validated['nama_sales'] ?? null,
                    'tanggal_bergabung' => $validated['tanggal_bergabung'],
                    'diskon_tipe' => $validated['diskon_tipe'] ?? null,
                    'diskon_nilai' => $validated['diskon_nilai'] ?? 0,
                    'diskon_ball_tipe' => $validated['diskon_ball_tipe'] ?? null,
                    'diskon_ball_nilai' => $validated['diskon_ball_nilai'] ?? 0,
                    'nama_ekspedisi' => $validated['nama_ekspedisi'] ?? null,
                    'ongkir' => $validated['ongkir'] ?? 0,
                    'notes' => $validated['notes'] ?? null,
                    'diperbaharui' => $today,
                ]);

                // Proses items dan update stok
                foreach ($validated['items'] as $itemData) {
                    $stockItem = StockItem::find($itemData['stock_item_id']);
                    
                    if (!$stockItem) {
                        throw new \Exception('Stock item tidak ditemukan');
                    }

                    $beforeStock = $stockItem->tersedia;
                    // Izinkan minus untuk preorder
                    $stockItem->tersedia = $stockItem->tersedia - $itemData['jumlah'];
                    $stockItem->diperbaharui = $today;
                    $stockItem->save();

                    // Log history stok berkurang karena pembelian client
                    $action = 'Stok Berkurang (Pembelian Client)';
                    $changes = [
                        'stok' => [
                            'stok_lama' => (int) $beforeStock,
                            'stok_baru' => - (int) $itemData['jumlah'],
                            'stok_total' => (int) $stockItem->tersedia,
                        ],
                        'catatan' => 'Dikurangi ' . (int) $itemData['jumlah'] . ' untuk client ' . $client->nama . ' (' . $client->client_id . ')',
                    ];
                    if ($beforeStock < $itemData['jumlah']) {
                        $changes['preorder'] = true;
                        $changes['keterangan'] = 'Preorder (hutang sampai stok diisi)';
                        $action = 'Preorder - Stok Negatif (Pembelian Client)';
                    }
                    $this->logStockHistory($stockItem, $action, $changes);

                    // Buat client item
                    ClientItem::create([
                        'client_id' => $client->id,
                        'stock_item_id' => $itemData['stock_item_id'],
                        'jumlah' => $itemData['jumlah'],
                        'harga_satuan' => $stockItem->harga,
                        'total_harga' => $stockItem->harga * $itemData['jumlah'],
                    ]);

                    // Pada setiap proses items (baik create maupun update), tambahkan update ke ClientStockItem
                    // Bagian store (client baru atau update existing)
                    // Setelah ClientItem::create atau update, tambahkan kode berikut:
                    $existingStock = ClientStockItem::where('client_id', $client->id)
                        ->where('nama', $stockItem->nama)
                        ->where('sku', $stockItem->sku)
                        ->first();
                    if ($existingStock) {
                        $existingStock->tersedia += $itemData['jumlah'];
                        $existingStock->harga = $stockItem->harga;
                        $existingStock->diperbaharui = $today;
                        $existingStock->save();
                    } else {
                        ClientStockItem::create([
                            'client_id' => $client->id,
                            'nama' => $stockItem->nama,
                            'sku' => $stockItem->sku,
                            'kategori' => $stockItem->kategori ?? 'Umum',
                            'tersedia' => $itemData['jumlah'],
                            'harga' => $stockItem->harga,
                            'diperbaharui' => $today,
                        ]);
                    }
                }

                // Log history create
                $this->logHistory($client, 'Client Baru Dibuat', [
                    'nama' => $client->nama,
                    'client_id' => $client->client_id,
                    'alamat' => $client->alamat,
                    'telepon' => $client->telepon,
                    'nama_sales' => $validated['nama_sales'] ?? null,
                    'tanggal_bergabung' => $client->tanggal_bergabung->format('Y-m-d'),
                    'items' => $validated['items'],
                    'diskon_tipe' => $validated['diskon_tipe'] ?? null,
                    'diskon_nilai' => $validated['diskon_nilai'] ?? 0,
                    'diskon_ball_tipe' => $validated['diskon_ball_tipe'] ?? null,
                    'diskon_ball_nilai' => $validated['diskon_ball_nilai'] ?? 0,
                    'nama_ekspedisi' => $validated['nama_ekspedisi'] ?? null,
                    'ongkir' => $validated['ongkir'] ?? 0,
                    'notes' => $validated['notes'] ?? null,
                ]);

                DB::commit();

                // Return client dengan items
                $client->load(['items.stockItem']);
                return response()->json($this->serializeClient($client), 201);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $client = Client::where('client_id', $id)->first();
        if (!$client) {
            return response()->json(['message' => 'Client tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'client_id' => 'required|string|max:50|unique:clients,client_id,' . $client->id,
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:30',
            'nama_sales' => 'nullable|string|max:100',
            'tanggal_bergabung' => 'required|date',
            'diskon_tipe' => 'nullable|in:rupiah,persen',
            'diskon_nilai' => 'nullable|numeric|min:0',
            'diskon_ball_tipe' => 'nullable|in:rupiah,persen',
            'diskon_ball_nilai' => 'nullable|numeric|min:0',
            'nama_ekspedisi' => 'nullable|string|max:150',
            'ongkir' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.stock_item_id' => 'required|integer|exists:stock_items,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $today = now()->toDateString();

            // Buat mapping untuk items lama dan baru
            $oldItemsMap = [];
            foreach ($client->items as $oldItem) {
                $oldItemsMap[$oldItem->stock_item_id] = $oldItem->jumlah;
            }

            $newItemsMap = [];
            foreach ($validated['items'] as $itemData) {
                $newItemsMap[$itemData['stock_item_id']] = $itemData['jumlah'];
            }

            // Hitung selisih untuk setiap item
            $allItemIds = array_unique(array_merge(array_keys($oldItemsMap), array_keys($newItemsMap)));
            
            foreach ($allItemIds as $itemId) {
                $oldQty = $oldItemsMap[$itemId] ?? 0;
                $newQty = $newItemsMap[$itemId] ?? 0;
                $selisih = $newQty - $oldQty;

                $stockItem = StockItem::find($itemId);
                if (!$stockItem) continue;

                $beforeStock = $stockItem->tersedia;

                if ($selisih > 0) {
                    // Stok bertambah (client mendapat lebih banyak) - admin stok berkurang
                    $stockItem->tersedia = $stockItem->tersedia - $selisih;
                    $stockItem->diperbaharui = $today;
                    $stockItem->save();

                    // Log history stok berkurang
                    $action = 'Stok Berkurang (Edit Client)';
                    $changes = [
                        'stok' => [
                            'stok_lama' => (int) $beforeStock,
                            'stok_baru' => - (int) $selisih,
                            'stok_total' => (int) $stockItem->tersedia,
                        ],
                        'catatan' => 'Dikurangi ' . (int) $selisih . ' untuk client ' . $client->nama . ' (' . $client->client_id . ')',
                    ];
                    if ($beforeStock < $selisih) {
                        $changes['preorder'] = true;
                        $changes['keterangan'] = 'Preorder (hutang sampai stok diisi)';
                        $action = 'Preorder - Stok Negatif (Edit Client)';
                    }
                    $this->logStockHistory($stockItem, $action, $changes);

                } elseif ($selisih < 0) {
                    // Stok berkurang (client mendapat lebih sedikit) - kembalikan ke admin
                    $returnQty = abs($selisih);
                    $stockItem->tersedia = $stockItem->tersedia + $returnQty;
                    $stockItem->diperbaharui = $today;
                    $stockItem->save();

                    // Log history pengembalian stok
                    $this->logStockHistory(
                        $stockItem,
                        'Stok Dikembalikan dari Client (Edit Client)',
                        [
                            'stok' => [
                                'stok_lama' => (int) $beforeStock,
                                'stok_baru' => (int) $returnQty,
                                'stok_total' => (int) $stockItem->tersedia,
                            ],
                            'catatan' => 'Dikembalikan ' . (int) $returnQty . ' dari client ' . $client->nama . ' (' . $client->client_id . ')',
                        ]
                    );
                }
                // Jika $selisih == 0, tidak ada perubahan
            }

            // Update client
            $client->update([
                'nama' => $validated['nama'],
                'client_id' => $validated['client_id'],
                'alamat' => $validated['alamat'],
                'telepon' => $validated['telepon'],
                'nama_sales' => $validated['nama_sales'] ?? null,
                'tanggal_bergabung' => $validated['tanggal_bergabung'],
                'diskon_tipe' => $validated['diskon_tipe'] ?? null,
                'diskon_nilai' => $validated['diskon_nilai'] ?? 0,
                'diskon_ball_tipe' => $validated['diskon_ball_tipe'] ?? null,
                'diskon_ball_nilai' => $validated['diskon_ball_nilai'] ?? 0,
                'nama_ekspedisi' => $validated['nama_ekspedisi'] ?? null,
                'ongkir' => $validated['ongkir'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'diperbaharui' => $today,
            ]);

            // Hapus items lama
            $client->items()->delete();
            
            // Hapus juga ClientStockItem lama
            ClientStockItem::where('client_id', $client->id)->delete();

            // Buat client items baru
            foreach ($validated['items'] as $itemData) {
                $stockItem = StockItem::find($itemData['stock_item_id']);
                
                if (!$stockItem) {
                    throw new \Exception('Stock item tidak ditemukan');
                }

                // Buat client item baru
                ClientItem::create([
                    'client_id' => $client->id,
                    'stock_item_id' => $itemData['stock_item_id'],
                    'jumlah' => $itemData['jumlah'],
                    'harga_satuan' => $stockItem->harga,
                    'total_harga' => $stockItem->harga * $itemData['jumlah'],
                ]);

                // Pada setiap proses items (baik create maupun update), tambahkan update ke ClientStockItem
                // Bagian store (client baru atau update existing)
                // Setelah ClientItem::create atau update, tambahkan kode berikut:
                $existingStock = ClientStockItem::where('client_id', $client->id)
                    ->where('nama', $stockItem->nama)
                    ->where('sku', $stockItem->sku)
                    ->first();
                if ($existingStock) {
                    $existingStock->tersedia += $itemData['jumlah'];
                    $existingStock->harga = $stockItem->harga;
                    $existingStock->diperbaharui = $today;
                    $existingStock->save();
                } else {
                    ClientStockItem::create([
                        'client_id' => $client->id,
                        'nama' => $stockItem->nama,
                        'sku' => $stockItem->sku,
                        'kategori' => $stockItem->kategori ?? 'Umum',
                        'tersedia' => $itemData['jumlah'],
                        'harga' => $stockItem->harga,
                        'diperbaharui' => $today,
                    ]);
                }
            }

            // Log history update
            $this->logHistory($client, 'Client Diperbaharui', [
                'nama' => $client->nama,
                'client_id' => $client->client_id,
                'alamat' => $client->alamat,
                'telepon' => $client->telepon,
                'nama_sales' => $validated['nama_sales'] ?? null,
                'tanggal_bergabung' => $client->tanggal_bergabung->format('Y-m-d'),
                'items' => $validated['items']
            ]);

            DB::commit();

            // Return client dengan items
            $client->load(['items.stockItem']);
            return response()->json($this->serializeClient($client));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        $client = Client::where('client_id', $id)->first();
        if (!$client) {
            return response()->json(['message' => 'Client tidak ditemukan'], 404);
        }

        DB::beginTransaction();
        try {
            $today = now()->toDateString();

            // Kembalikan stok ke dashboard
            foreach ($client->items as $item) {
                $stockItem = StockItem::find($item->stock_item_id);
                if ($stockItem) {
                    $beforeStock = $stockItem->tersedia;
                    $stockItem->tersedia += $item->jumlah;
                    $stockItem->diperbaharui = $today;
                    $stockItem->save();

                    // Log history pengembalian stok karena client dihapus
                    $this->logStockHistory(
                        $stockItem,
                        'Stok Dikembalikan dari Client (Client Dihapus)',
                        [
                            'stok' => [
                                'stok_lama' => (int) $beforeStock,
                                'stok_baru' => (int) $item->jumlah,
                                'stok_total' => (int) $stockItem->tersedia,
                            ],
                            'catatan' => 'Pengembalian ' . (int) $item->jumlah . ' dari client ' . $client->nama . ' (' . $client->client_id . ')',
                        ]
                    );
                }
            }

            // Hapus juga ClientStockItem lama
            ClientStockItem::where('client_id', $client->id)->delete();

            // Log history delete
            $this->logHistory($client, 'Client Dihapus', [
                'catatan' => 'Client "' . $client->nama . '" dihapus dari sistem'
            ]);

            // Hapus client (items akan terhapus otomatis karena cascade)
            $client->delete();
            
            // Hapus juga ClientStockItem
            ClientStockItem::where('client_id', $client->id)->delete();

            DB::commit();
            return response()->json(['message' => 'Client dihapus']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    private function serializeClient(Client $client): array
    {
        return [
            'id' => $client->client_id,
            'nama' => $client->nama,
            'alamat' => $client->alamat,
            'telepon' => $client->telepon,
            'nama_sales' => $client->nama_sales,
            'diskon_tipe' => $client->diskon_tipe,
            'diskon_nilai' => $client->diskon_nilai,
            'diskon_ball_tipe' => $client->diskon_ball_tipe,
            'diskon_ball_nilai' => $client->diskon_ball_nilai,
            'nama_ekspedisi' => $client->nama_ekspedisi,
            'ongkir' => $client->ongkir,
            'notes' => $client->notes,
            'bergabung' => $client->tanggal_bergabung->format('Y-m-d'),
            'diperbaharui' => $client->diperbaharui->format('Y-m-d'),
            'items' => $client->items->map(function (ClientItem $item) {
                return [
                    'stokId' => $item->stock_item_id,
                    'stokNama' => $item->stockItem->nama,
                    'stokJumlah' => $item->jumlah,
                    'harga_satuan' => $item->harga_satuan,
                    'total_harga' => $item->total_harga,
                ];
            }),
            'created_at' => $client->created_at?->toISOString(),
            'updated_at' => $client->updated_at?->toISOString(),
        ];
    }

    private function logHistory(Client $client, string $action, array|string $changes): void
    {
        ClientHistory::create([
            'client_id' => $client->id,
            'action' => $action,
            'changes' => is_array($changes) ? $changes : ['catatan' => (string) $changes],
            'user' => optional(auth()->user())->name ?? 'Admin',
        ]);
    }

    private function logStockHistory(StockItem $item, string $action, array|string $changes): void
    {
        StockItemHistory::create([
            'stock_item_id' => $item->id,
            'nama_item' => $item->nama, // Tambahkan nama item
            'tersedia' => $item->tersedia, // Tambahkan stok tersedia saat ini
            'action' => $action,
            'changes' => is_array($changes) ? $changes : ['catatan' => (string) $changes],
            'user' => optional(auth()->user())->name ?? 'Admin',
        ]);
    }
}
