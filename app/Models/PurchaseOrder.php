<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'po_number',
        'total_amount',
        'status',
        'payment_status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the user (client account) that owns the purchase order.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the items for the purchase order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Get stock items for the purchase order.
     */
    public function stockItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class)->where('item_type', 'stock');
    }

    /**
     * Get external items for the purchase order.
     */
    public function externalItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class)->where('item_type', 'external');
    }

    /**
     * Scope a query to only include pending purchase orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved purchase orders.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include received purchase orders.
     */
    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    /**
     * Scope a query to only include cancelled purchase orders.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
