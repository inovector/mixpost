<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Jobs\ProcessTwitterMetricsJob;
use Inovector\Mixpost\Traits\Command\AccountsOption;

class ProcessMetrics extends Command
{
    use AccountsOption;

    public $signature = 'mixpost:process-metrics {--accounts=}';

    public $description = 'Process metrics for the social providers';

    public function handle(): int
    {
        $this->accounts()->each(function ($account) {
            $job = match ($account->provider) {
                'twitter' => ProcessTwitterMetricsJob::class,
                default => null,
            };

            if ($job) {
                $job::dispatch($account);
            }
        });

        return self::SUCCESS;
    }
}
