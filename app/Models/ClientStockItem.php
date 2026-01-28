<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientStockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'nama',
        'sku',
        'kategori',
        'tersedia',
        'harga',
        'diperbaharui',
    ];

    protected $casts = [
        'diperbaharui' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
