<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientSale extends Model
{
    protected $fillable = [
        'client_id',
        'order_number',
        'total_items',
        'total_quantity',
        'total_amount',
        'payment_method',
        'amount_paid',
        'change_amount',
        'status',
        'sale_date',
        'notes',
        'customer_phone',
        'customer_address',
        'is_deleted',
        'deleted_at',
        'deleted_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'integer',
        'amount_paid' => 'integer',
        'change_amount' => 'integer',
        'is_deleted' => 'integer',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ClientSaleItem::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
