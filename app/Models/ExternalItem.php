<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'item_name',
        'sku',
        'description',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the client that owns the external item.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
