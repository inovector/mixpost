<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Jobs\ProcessTwitterMetricsJob;
use Inovector\Mixpost\Models\Account;

class ProcessMetrics extends Command
{
    public $signature = 'mixpost:process-metrics';

    public $description = 'Process metrics for the social providers';

    public function handle(): int
    {
        Account::all()->each(function ($account) {
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
