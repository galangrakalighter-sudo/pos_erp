<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StockNotification;
use App\Models\StockItem;
use App\Models\ClientStockItem;
use App\Models\User;

class GenerateStockNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:generate-stock {--user-id= : Generate for specific user ID} {--threshold=500 : Threshold for low stock}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate low stock notifications for all users or specific user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $threshold = (int) $this->option('threshold');
        
        $this->info("Generating stock notifications with threshold: {$threshold}");
        
        if ($userId) {
            // Generate untuk user tertentu
            $user = User::find($userId);
            if (!$user) {
                $this->error("User dengan ID {$userId} tidak ditemukan");
                return 1;
            }
            
            $this->generateForUser($user, $threshold);
            $this->info("Notifications generated for user: {$user->name}");
        } else {
            // Generate untuk semua user
            $users = User::all();
            $this->info("Generating notifications for {$users->count()} users...");
            
            foreach ($users as $user) {
                $this->generateForUser($user, $threshold);
            }
            
            $this->info("Notifications generated for all users");
        }
        
        // Hapus notifikasi lama yang sudah di-dismiss
        $deleted = StockNotification::where('is_dismissed', true)
            ->where('created_at', '<', now()->subDays(1))
            ->delete();
            
        if ($deleted > 0) {
            $this->info("Cleaned up {$deleted} old dismissed notifications");
        }
        
        return 0;
    }
    
    private function generateForUser(User $user, int $threshold)
    {
        // Generate notifikasi untuk admin stock
        $adminItems = StockItem::where('tersedia', '<=', $threshold)->get();
        
        foreach ($adminItems as $item) {
            // Cek apakah sudah ada notifikasi untuk item ini
            $existingNotification = StockNotification::where('user_id', $user->id)
                ->where('stock_item_id', $item->id)
                ->where('item_type', 'admin')
                ->first();

            if ($existingNotification) {
                // Update notifikasi existing jika stock berubah
                if ($existingNotification->current_stock != $item->tersedia) {
                    $existingNotification->update([
                        'current_stock' => $item->tersedia,
                        'notification_type' => $item->tersedia <= 0 ? 'out_of_stock' : 'low_stock',
                        'threshold' => $threshold
                    ]);
                }
            } else {
                // Buat notifikasi baru jika belum ada
                StockNotification::create([
                    'user_id' => $user->id,
                    'stock_item_id' => $item->id,
                    'item_type' => 'admin',
                    'notification_type' => $item->tersedia <= 0 ? 'out_of_stock' : 'low_stock',
                    'item_name' => $item->nama,
                    'sku' => $item->sku,
                    'current_stock' => $item->tersedia,
                    'threshold' => $threshold,
                    'is_dismissed' => false
                ]);
            }
        }
        
        // Generate notifikasi untuk client stock (GAFI)
        $clientItems = ClientStockItem::where('kategori', 'GAFI')
            ->where('tersedia', '<=', $threshold)
            ->get();
        
        foreach ($clientItems as $item) {
            // Cek apakah sudah ada notifikasi untuk item ini
            $existingNotification = StockNotification::where('user_id', $user->id)
                ->where('stock_item_id', $item->id)
                ->where('item_type', 'client')
                ->first();

            if ($existingNotification) {
                // Update notifikasi existing jika stock berubah
                if ($existingNotification->current_stock != $item->tersedia) {
                    $existingNotification->update([
                        'current_stock' => $item->tersedia,
                        'notification_type' => $item->tersedia <= 0 ? 'out_of_stock' : 'low_stock',
                        'threshold' => $threshold
                    ]);
                }
            } else {
                // Buat notifikasi baru jika belum ada
                StockNotification::create([
                    'user_id' => $user->id,
                    'stock_item_id' => $item->id,
                    'item_type' => 'client',
                    'notification_type' => $item->tersedia <= 0 ? 'out_of_stock' : 'low_stock',
                    'item_name' => $item->nama,
                    'sku' => $item->sku,
                    'current_stock' => $item->tersedia,
                    'threshold' => $threshold,
                    'is_dismissed' => false
                ]);
            }
        }
    }
}
