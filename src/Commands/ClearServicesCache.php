<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Facades\Services;

class ClearServicesCache extends Command
{
    public $signature = 'mixpost:clear-services-cache';

    public $description = 'Clear the services from cache';

    public function handle(): int
    {
        Services::forgetAll();

        $this->info('Cache services has been cleared!');

        return self::SUCCESS;
    }
}
