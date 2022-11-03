<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Inovector\Mixpost\Facades\Settings;

class ClearSettingsCache extends Command
{
    public $signature = 'mixpost:clear-settings-cache';

    public $description = 'Publish compiled assets to your public folder';

    public function handle(): int
    {
        Settings::clearCache();

        $this->info('Cache settings has been cleared!');

        return self::SUCCESS;
    }
}
