<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Concerns\AccountsOption;
use Inovector\Mixpost\SocialProviders\Mastodon\Jobs\ImportMastodonFollowersJob;
use Inovector\Mixpost\SocialProviders\Meta\Jobs\ImportFacebookPageFollowersJob;
use Inovector\Mixpost\SocialProviders\Twitter\Jobs\ImportTwitterFollowersJob;

class ImportAccountAudience extends Command
{
    use AccountsOption;

    public $signature = 'mixpost:import-account-audience {--accounts=}';

    public $description = 'Import audience(count of followers, fans...etc.) for the social providers';

    public function handle(): int
    {
        $this->accounts()->each(function ($account) {
            $job = match ($account->provider) {
                'twitter' => ImportTwitterFollowersJob::class,
                'facebook_page' => ImportFacebookPageFollowersJob::class,
                'mastodon' => ImportMastodonFollowersJob::class,
                default => null,
            };

            if ($job) {
                $job::dispatch($account);
            }
        });

        return self::SUCCESS;
    }
}
