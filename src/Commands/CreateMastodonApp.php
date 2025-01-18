<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Actions\CreateMastodonApp as CreateMastodonAppAction;
use Inovector\Mixpost\Facades\ServiceManager;

class CreateMastodonApp extends Command
{
    public $signature = 'mixpost:create-mastodon-app {server}';

    public $description = 'Create new mastodon application for a server';

    public function handle(): int
    {
        $server = $this->argument('server');

        $serviceName = "mastodon.$server";

        if (ServiceManager::get($serviceName)) {
            if (!$this->confirm('Are you sure you want to create a new application for this server?')) {
                return self::FAILURE;
            }

            $this->comment("This action may have a negative impact on scheduled posts and authenticated accounts with Mastodon on $server server.");

            if (!$this->confirm('I confirm that I understand the risks and I will reauthenticate all accounts on this Mastodon server.')) {
                return self::FAILURE;
            }
        }

        $result = (new CreateMastodonAppAction())($server);

        if (isset($result['error'])) {
            $this->error($result['error']);

            return self::FAILURE;
        }

        $this->info("A new application for the $server server has been created!");

        return self::SUCCESS;
    }
}
