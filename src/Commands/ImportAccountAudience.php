<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Jobs\ImportFacebookGroupMembersJob;
use Inovector\Mixpost\Jobs\ImportFacebookPageFollowersJob;
use Inovector\Mixpost\Jobs\ImportTwitterFollowersJob;
use Inovector\Mixpost\Traits\Command\AccountsOption;

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
                'facebook_group' => ImportFacebookGroupMembersJob::class,
                default => null,
            };

            if ($job) {
                $job::dispatch($account);
            }
        });

        return self::SUCCESS;
    }
}
