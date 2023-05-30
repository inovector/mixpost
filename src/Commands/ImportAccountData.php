<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Concerns\AccountsOption;
use Inovector\Mixpost\Jobs\ImportFacebookInsightsJob;
use Inovector\Mixpost\Jobs\ImportMastodonPostsJob;
use Inovector\Mixpost\Jobs\ImportTwitterPostsJob;

class ImportAccountData extends Command
{
    use AccountsOption;

    public $signature = 'mixpost:import-account-data {--accounts=}';

    public $description = 'Import data from social service providers';

    public function handle(): int
    {
        $this->accounts()->each(function ($account) {
            $job = match ($account->provider) {
                'twitter' => ImportTwitterPostsJob::class,
                'facebook_page' => ImportFacebookInsightsJob::class,
                'mastodon' => ImportMastodonPostsJob::class,
                default => null,
            };

            if ($job) {
                $job::dispatch($account);
            }
        });

        return self::SUCCESS;
    }
}
