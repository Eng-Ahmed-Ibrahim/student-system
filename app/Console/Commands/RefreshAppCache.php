<?php 
namespace App\Console\Commands;

use App\Models\Setting;
use App\Helpers\Helpers;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RefreshAppCache extends Command
{
    protected $signature = 'custom:refresh-cache';
    
    protected $description = 'Rebuild custom app cache (settings, categories, etc)';

    public function handle()
    {
        $this->info('🔁 Refreshing custom cache...');

        Helpers::recache_groups();
        Helpers::recache_students();

        
        $this->info('✅ Custom cache updated!');
    }
}
