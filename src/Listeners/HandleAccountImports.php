<?php

namespace Inovector\Mixpost\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Inovector\Mixpost\Commands\ImportAccountAudience;
use Inovector\Mixpost\Commands\ImportAccountData;
use Inovector\Mixpost\Commands\ProcessMetrics;

class HandleAccountImports implements ShouldQueue
{
    public function handle(object $event): void
    {
        Artisan::call(ImportAccountAudience::class, [
            '--accounts' => $event->account->id,
        ]);

        Artisan::call(ImportAccountData::class, [
            '--accounts' => $event->account->id,
        ]);

        Artisan::call(ProcessMetrics::class, [
            '--accounts' => $event->account->id,
        ]);
    }
}
