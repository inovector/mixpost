<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Facades\ServiceManager;

class ClearServicesCache extends Command
{
    public $signature = 'mixpost:clear-services-cache';

    public $description = 'Clear the services from cache';

    public function handle(): int
    {
        ServiceManager::forgetAll();

        $this->info('Cache services has been cleared!');

        return self::SUCCESS;
    }
}
