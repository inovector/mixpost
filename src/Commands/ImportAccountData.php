<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Concerns\AccountsOption;
use Inovector\Mixpost\SocialProviders\Mastodon\Jobs\ImportMastodonPostsJob;
use Inovector\Mixpost\SocialProviders\Meta\Jobs\ImportFacebookInsightsJob;
use Inovector\Mixpost\SocialProviders\Twitter\Jobs\ImportTwitterPostsJob;

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
