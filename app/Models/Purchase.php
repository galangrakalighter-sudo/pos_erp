<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'purchase_number',
        'supplier_name',
        'supplier_contact',
        'invoice_number',
        'total_amount',
        'status',
        'payment_status',
        'notes',
        'purchase_date',
        'due_date',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'purchase_date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Get the admin that created the purchase.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the items for the purchase.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Scope a query to only include pending purchases.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved purchases.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include completed purchases.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include rejected purchases.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
