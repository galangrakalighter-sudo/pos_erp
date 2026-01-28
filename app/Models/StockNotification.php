<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stock_item_id',
        'item_type',
        'notification_type',
        'item_name',
        'sku',
        'current_stock',
        'threshold',
        'is_dismissed',
        'dismissed_at',
        'dismissed_by'
    ];

    protected $casts = [
        'is_dismissed' => 'boolean',
        'dismissed_at' => 'datetime',
        'current_stock' => 'integer',
        'threshold' => 'integer'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dismissedBy()
    {
        return $this->belongsTo(User::class, 'dismissed_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_dismissed', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeLowStock($query)
    {
        return $query->where('notification_type', 'low_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('notification_type', 'out_of_stock');
    }

    // Methods
    public function dismiss($userId = null)
    {
        $this->update([
            'is_dismissed' => true,
            'dismissed_at' => now(),
            'dismissed_by' => $userId
        ]);
    }

    public function reactivate()
    {
        $this->update([
            'is_dismissed' => false,
            'dismissed_at' => null,
            'dismissed_by' => null
        ]);
    }

    // Static methods untuk generate notifikasi
    public static function generateLowStockNotifications($userId = null, $adminThreshold = null, $clientThreshold = null)
    {
        // Hapus notifikasi lama yang sudah di-dismiss
        self::where('is_dismissed', true)
            ->where('created_at', '<', now()->subDays(1))
            ->delete();

        // HAPUS NOTIFIKASI YANG TIDAK SESUAI THRESHOLD BARU
        if ($userId) {
            // Hapus notifikasi admin yang tidak sesuai threshold baru
            if ($adminThreshold !== null) {
                self::where('user_id', $userId)
                    ->where('item_type', 'admin')
                    ->where('threshold', '!=', $adminThreshold)
                    ->delete();
            }
            
            // Hapus notifikasi client yang tidak sesuai threshold baru
            if ($clientThreshold !== null) {
                self::where('user_id', $userId)
                    ->where('item_type', 'client')
                    ->where('threshold', '!=', $clientThreshold)
                    ->delete();
            }
        }

        // Generate notifikasi untuk admin stock (hanya jika adminThreshold ada)
        if ($adminThreshold !== null) {
            $adminItems = StockItem::where('tersedia', '<=', $adminThreshold)->get();

            foreach ($adminItems as $item) {
                // Cek apakah sudah ada notifikasi untuk item ini
                $existingNotification = self::where('user_id', $userId)
                    ->where('stock_item_id', $item->id)
                    ->where('item_type', 'admin')
                    ->first();

                if ($existingNotification) {
                    // Update notifikasi existing jika stock berubah atau threshold berubah
                    if ($existingNotification->current_stock != $item->tersedia || $existingNotification->threshold != $adminThreshold) {
                        $existingNotification->update([
                            'current_stock' => $item->tersedia,
                            'notification_type' => $item->tersedia <= 0 ? 'out_of_stock' : 'low_stock',
                            'threshold' => $adminThreshold,
                            'is_dismissed' => false // Reset dismiss status jika threshold berubah
                        ]);
                    }
                } else {
                    // Buat notifikasi baru jika belum ada
                    self::create([
                        'user_id' => $userId,
                        'stock_item_id' => $item->id,
                        'item_type' => 'admin',
                        'notification_type' => $item->tersedia <= 0 ? 'out_of_stock' : 'low_stock',
                        'item_name' => $item->nama,
                        'sku' => $item->sku,
                        'current_stock' => $item->tersedia,
                        'threshold' => $adminThreshold,
                        'is_dismissed' => false
                    ]);
                }
            }
        }

        // Generate notifikasi untuk client stock (GAFI) - hanya jika clientThreshold ada
        if ($clientThreshold !== null) {
            $clientItems = ClientStockItem::where('kategori', 'GAFI')
                ->where('tersedia', '<=', $clientThreshold)
                ->get();

            foreach ($clientItems as $item) {
                // Cek apakah sudah ada notifikasi untuk item ini
                $existingNotification = self::where('user_id', $userId)
                    ->where('stock_item_id', $item->id)
                    ->where('item_type', 'client')
                    ->first();

                if ($existingNotification) {
                    // Update notifikasi existing jika stock berubah atau threshold berubah
                    if ($existingNotification->current_stock != $item->tersedia || $existingNotification->threshold != $clientThreshold) {
                        $existingNotification->update([
                            'current_stock' => $item->tersedia,
                            'notification_type' => $item->tersedia <= 0 ? 'out_of_stock' : 'low_stock',
                            'threshold' => $clientThreshold,
                            'is_dismissed' => false // Reset dismiss status jika threshold berubah
                        ]);
                    }
                } else {
                    // Buat notifikasi baru jika belum ada
                    self::create([
                        'user_id' => $userId,
                        'stock_item_id' => $item->id,
                        'item_type' => 'client',
                        'notification_type' => $item->tersedia <= 0 ? 'out_of_stock' : 'low_stock',
                        'item_name' => $item->nama,
                        'sku' => $item->sku,
                        'current_stock' => $item->tersedia,
                        'threshold' => $clientThreshold,
                        'is_dismissed' => false
                    ]);
                }
            }
        }
    }
}
