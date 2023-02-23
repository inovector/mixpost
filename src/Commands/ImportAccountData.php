<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Jobs\ImportTwitterPostsJob;
use Inovector\Mixpost\Models\Account;

class ImportAccountData extends Command
{
    public $signature = 'mixpost:import-account-data';

    public $description = 'Import data from social service providers';

    public function handle(): int
    {
        Account::all()->each(function ($account) {
            $job = match ($account->provider) {
                'twitter' => ImportTwitterPostsJob::class,
                default => null,
            };

            if ($job) {
                $job::dispatch($account);
            }
        });

        return self::SUCCESS;
    }
}
