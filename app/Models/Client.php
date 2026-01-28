<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'client_id',
        'alamat',
        'telepon',
        'nama_sales',
        'diskon_tipe',
        'diskon_nilai',
        'diskon_ball_tipe',
        'diskon_ball_nilai',
        'nama_ekspedisi',
        'ongkir',
        'notes',
        'tanggal_bergabung',
        'diperbaharui',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'diperbaharui' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(ClientItem::class);
    }

    public function stockItems()
    {
        return $this->belongsToMany(StockItem::class, 'client_items')
                    ->withPivot('jumlah', 'harga_satuan', 'total_harga')
                    ->withTimestamps();
    }

    public function clientStockItems()
    {
        return $this->hasMany(ClientStockItem::class);
    }

    public function histories()
    {
        return $this->hasMany(ClientHistory::class);
    }

    /**
     * Get the user associated with this client.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'client_id', 'client_id');
    }
}
