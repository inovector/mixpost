<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Facades\Settings;

class ClearSettingsCache extends Command
{
    public $signature = 'mixpost:clear-settings-cache';

    public $description = 'Clear the settings from cache';

    public function handle(): int
    {
        Settings::forgetAll();

        $this->info('Cache settings has been cleared!');

        return self::SUCCESS;
    }
}
