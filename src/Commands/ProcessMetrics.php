<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Concerns\AccountsOption;
use Inovector\Mixpost\SocialProviders\Mastodon\Jobs\ProcessMastodonMetricsJob;
use Inovector\Mixpost\SocialProviders\Twitter\Jobs\ProcessTwitterMetricsJob;

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
                'mastodon' => ProcessMastodonMetricsJob::class,
                default => null,
            };

            if ($job) {
                $job::dispatch($account);
            }
        });

        return self::SUCCESS;
    }
}
