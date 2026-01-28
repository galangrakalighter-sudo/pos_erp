<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'nama_pemesan',
        'id_pesanan',
        'nama_sales',
        'jenis_transaksi',
        'telepon',
        'alamat',
        'status',
        'periode',
        'diskon_tipe',
        'diskon_nilai',
        'diskon_ball_tipe',
        'diskon_ball_nilai',
        'nama_ekspedisi',
        'ongkir',
        'notes',
        'total_quantity',
        'total_diskon',
        'total_diskon_ball',
        'total_harga',
    ];

    public function items()
    {
        return $this->belongsToMany(StockItem::class, 'sale_items')
            ->withPivot(['quantity', 'harga', 'subtotal'])
            ->withTimestamps();
    }
}
