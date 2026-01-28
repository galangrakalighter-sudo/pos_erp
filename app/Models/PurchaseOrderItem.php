<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'stock_item_id',
        'item_name',
        'sku',
        'quantity',
        'unit_price',
        'subtotal',
        'item_type',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the purchase order that owns the item.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the stock item (if this is a stock item).
     */
    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    /**
     * Scope a query to only include stock items.
     */
    public function scopeStock($query)
    {
        return $query->where('item_type', 'stock');
    }

    /**
     * Scope a query to only include external items.
     */
    public function scopeExternal($query)
    {
        return $query->where('item_type', 'external');
    }
}
