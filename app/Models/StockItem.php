<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    /** @use HasFactory<\Database\Factories\StockItemFactory> */
    use HasFactory;

    protected $fillable = [
        'nama',
        'sku',
        'kondisi',
        'lokasi',
        'tersedia',
        'disimpan',
        'harga',
        'diperbaharui',
        'gambar',
    ];

    protected $casts = [
        'diperbaharui' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'sale_items')
            ->withPivot(['quantity', 'harga', 'subtotal'])
            ->withTimestamps();
    }

    public function histories()
    {
        return $this->hasMany(StockItemHistory::class, 'stock_item_id');
    }

    /**
     * Kurangi stok dan catat histori untuk sales
     */
    public function decreaseStockForSale($quantity, $saleId, $clientName, $orderId)
    {
        $quantitySebelum = $this->tersedia;
        // Izinkan stok negatif (sama seperti di client)
        $this->tersedia = $this->tersedia - $quantity;
        $this->save();

        // Catat histori menggunakan StockItemHistory
        $this->logHistory('Stok Berkurang (Pembelian Sales)', [
            'stok_sebelum' => $quantitySebelum,
            'stok_berkurang' => $quantity,
            'stok_sesudah' => $this->tersedia,
            'keterangan' => "Dikurangi {$quantity} untuk client {$clientName} ({$orderId})",
            'sale_id' => $saleId,
            'referensi' => "SALE-{$saleId}"
        ]);

        return $this;
    }

    /**
     * Tambah stok dan catat histori (untuk refund/return)
     */
    public function increaseStockForRefund($quantity, $saleId, $orderId, $keterangan = null)
    {
        $quantitySebelum = $this->tersedia;
        $this->tersedia += $quantity;
        $this->save();

        // Catat histori menggunakan StockItemHistory
        $this->logHistory('Stok Dikembalikan (Refund)', [
            'stok_sebelum' => $quantitySebelum,
            'stok_ditambah' => $quantity,
            'stok_sesudah' => $this->tersedia,
            'keterangan' => $keterangan ?: "Stok dikembalikan karena penjualan {$orderId} dihapus",
            'sale_id' => $saleId,
            'referensi' => "REFUND-SALE-{$saleId}"
        ]);

        return $this;
    }

    /**
     * Method untuk mencatat histori menggunakan StockItemHistory
     */
    private function logHistory(string $action, array $changes): void
    {
        StockItemHistory::create([
            'stock_item_id' => $this->id,
            'nama_item' => $this->nama, // Tambahkan nama item
            'tersedia' => $this->tersedia, // Tambahkan stok tersedia saat ini
            'action' => $action,
            'changes' => $changes,
            'user' => optional(auth()->user())->name ?? 'Admin',
        ]);
    }
}
