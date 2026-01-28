<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StockNotification;
use App\Models\User;
use App\Models\StockItem;
use App\Models\ClientStockItem;

class StockNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus notifikasi lama
        StockNotification::truncate();
        
        // Ambil user admin dan client
        $admin = User::where('role', 'admin')->first();
        $client = User::where('role', 'client')->first();
        
        if (!$admin || !$client) {
            $this->command->error('User admin atau client tidak ditemukan. Jalankan UserSeeder terlebih dahulu.');
            return;
        }
        
        // Buat notifikasi untuk admin
        if ($admin) {
            // Notifikasi untuk stock admin yang low
            $adminItems = StockItem::where('tersedia', '<=', 500)->take(3)->get();
            
            foreach ($adminItems as $item) {
                StockNotification::create([
                    'user_id' => $admin->id,
                    'stock_item_id' => $item->id,
                    'item_type' => 'admin',
                    'notification_type' => $item->tersedia <= 0 ? 'out_of_stock' : 'low_stock',
                    'item_name' => $item->nama,
                    'sku' => $item->sku,
                    'current_stock' => $item->tersedia,
                    'threshold' => 500,
                    'is_dismissed' => false
                ]);
            }
        }
        
        // Buat notifikasi untuk client
        if ($client) {
            // Notifikasi untuk client stock yang low
            $clientItems = ClientStockItem::where('kategori', 'GAFI')
                ->where('tersedia', '<=', 500)
                ->take(2)
                ->get();
            
            foreach ($clientItems as $item) {
                StockNotification::create([
                    'user_id' => $client->id,
                    'stock_item_id' => $item->id,
                    'item_type' => 'client',
                    'notification_type' => $item->tersedia <= 0 ? 'out_of_stock' : 'low_stock',
                    'item_name' => $item->nama,
                    'sku' => $item->sku,
                    'current_stock' => $item->tersedia,
                    'threshold' => 500,
                    'is_dismissed' => false
                ]);
            }
        }
        
        $this->command->info('Stock notifications seeded successfully!');
    }
}
