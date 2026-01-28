<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientSaleItem extends Model
{
    protected $fillable = [
        'client_sale_id',
        'item_name',
        'item_sku',
        'quantity',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'discount_percent' => 'integer',
        'discount_amount' => 'integer',
        'subtotal' => 'integer',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(ClientSale::class, 'client_sale_id');
    }
}
