<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clear {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all data from database tables while preserving table structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to clear ALL data from the database? This action cannot be undone!')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting database cleanup...');

        try {
            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Get all table names
            $tables = DB::select('SHOW TABLES');
            $tableNames = [];
            
            foreach ($tables as $table) {
                $tableNames[] = array_values((array) $table)[0];
            }

            $this->info('Found ' . count($tableNames) . ' tables to clear.');

            // Clear all tables
            foreach ($tableNames as $tableName) {
                if ($tableName !== 'migrations') { // Skip migrations table
                    DB::table($tableName)->truncate();
                    $this->line("✓ Cleared table: {$tableName}");
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('✅ All database data has been cleared successfully!');
            $this->info('Table structures are preserved.');
            
        } catch (\Exception $e) {
            $this->error('❌ Error occurred while clearing database: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
