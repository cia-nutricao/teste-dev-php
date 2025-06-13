<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheService;

class ClearSupplierCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suppliers:cache-clear {--warm-up : Warm up cache after clearing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all supplier-related caches and optionally warm up essential data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing supplier caches...');
        
        try {
            CacheService::clearSupplierCaches();
            $this->info('✓ Supplier caches cleared successfully');
            
            if ($this->option('warm-up')) {
                $this->info('Warming up cache...');
                CacheService::warmUp();
                $this->info('✓ Cache warmed up successfully');
            }
            
            $this->newLine();
            $this->info('Cache operations completed!');
            
        } catch (\Exception $e) {
            $this->error('Failed to clear caches: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
