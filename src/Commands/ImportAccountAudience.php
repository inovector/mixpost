<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Jobs\ImportTwitterFollowersJob;
use Inovector\Mixpost\Models\Account;

class ImportAccountAudience extends Command
{
    public $signature = 'mixpost:import-account-audience';

    public $description = 'Import audience(count of followers, fans...etc.) for the social providers';

    public function handle(): int
    {
        Account::all()->each(function ($account) {
            $job = match ($account->provider) {
                'twitter' => ImportTwitterFollowersJob::class,
                default => null,
            };

            if ($job) {
                $job::dispatch($account);
            }
        });

        return self::SUCCESS;
    }
}
