<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'item_name',
        'item_description',
        'sku',
        'category',
        'quantity',
        'unit',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the purchase that owns the item.
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
}
