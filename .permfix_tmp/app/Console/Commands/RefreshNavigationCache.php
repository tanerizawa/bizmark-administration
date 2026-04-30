<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CacheHelper;

class RefreshNavigationCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:refresh-navigation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh navigation cache (nav counts, notifications, etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing navigation cache...');
        CacheHelper::clearNavigationCache();
        
        $this->info('Navigation cache cleared successfully!');
        $this->info('Cache will be regenerated on next page load.');
        
        return Command::SUCCESS;
    }
}
