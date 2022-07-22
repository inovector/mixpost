<?php

namespace Lao9s\Mixpost\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishAssetsCommand extends Command
{
    public $signature = 'mixpost:publish-assets';

    public $description = 'Publish compiled assets to your public folder';

    public function handle(): int
    {
        if (File::exists(public_path('vendor/mixpost'))) {
            $this->comment('Your application already have the mixpost assets');

            if (!$this->confirm('Do you want to rewrite?')) {
                return self::FAILURE;
            }
        }

        File::copyDirectory(__DIR__ . '/../../resources/dist/vendor', public_path('vendor'));

        $this->info('Assets was published to [public/vendor/mixpost]');

        return self::SUCCESS;
    }
}
