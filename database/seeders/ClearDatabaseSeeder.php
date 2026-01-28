<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear all data from tables in reverse dependency order
        // Start with child tables (those with foreign keys)
        
        // Clear client-related tables first
        DB::table('client_sale_items')->truncate();
        DB::table('client_sales')->truncate();
        DB::table('client_stock_items')->truncate();
        DB::table('client_identities')->truncate();
        DB::table('client_items')->truncate();
        DB::table('client_histories')->truncate();
        DB::table('clients')->truncate();
        
        // Clear sales-related tables
        DB::table('sale_items')->truncate();
        DB::table('sales')->truncate();
        
        // Clear stock-related tables
        DB::table('stock_item_histories')->truncate();
        DB::table('stock_items')->truncate();
        
        // Clear user table last (parent table)
        DB::table('users')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('All database data has been cleared successfully!');
    }
}
