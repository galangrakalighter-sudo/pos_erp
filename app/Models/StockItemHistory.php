<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItemHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_item_id',
        'nama_item',
        'tersedia',
        'action',
        'changes',
        'user',
    ];

    protected $casts = [
        'changes' => 'array',
    ];
} 