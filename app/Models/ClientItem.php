<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'stock_item_id',
        'jumlah',
        'harga_satuan',
        'total_harga',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }
}
