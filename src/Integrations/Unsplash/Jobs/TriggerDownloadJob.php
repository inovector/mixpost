<?php

namespace Inovector\Mixpost\Integrations\Unsplash\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Inovector\Mixpost\Integrations\Unsplash\Unsplash;

class TriggerDownloadJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly string $downloadLocation)
    {
    }

    public function handle(Unsplash $unsplash): void
    {
        $unsplash->downloadPhoto($this->downloadLocation);
    }
}
