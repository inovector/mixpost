<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Media;

class ChangeDisk extends Command
{
    public $signature = 'mixpost:change-disk {from} {to} {--force}';

    public $description = 'Create new mastodon application for a server';

    public function handle(): int
    {
        if (app()->isProduction() && !$this->option('force')) {
            $this->fail('The app is in production. Use --force if you are sure.');
        }

        $from = $this->argument('from');
        $to = $this->argument('to');

        if (!$from || !$to) {
            $this->fail('Please provide both source and target disk names.');
        }

        try {
            DB::transaction(function () use ($from, $to) {
                $bar = $this->output->createProgressBar(Media::count() + Account::count());
                $bar->start();

                Media::chunk(100, function (Collection $medias) use ($bar, $from, $to) {
                    foreach ($medias as $media) {
                        //the "disk" in mixpost_media table.,
                        if ($media->disk == $from) {
                            $media->disk = $to;
                        }
                        //conversions column of mixpost_media table has json stored disks too,
                        foreach ($media->conversions as $conversion) {
                            if (isset($conversion['disk']) && $conversion['disk'] == $from) {
                                $conversion['disk'] = $to;
                            }
                        }

                        if($media->isDirty())
                        {
                            $media->save();
                        }
                    }

                    // Advance the bar by the size of the chunked collection
                    $bar->advance($medias->count());
                });

                Account::chunk(100, function (Collection $accounts) use ($bar, $from, $to) {
                    foreach ($accounts as $account) {
                        //the disk in the media json of the mixpost_accounts table,
                        if ($account->media['disk'] == $from) {
                            $account->media['disk'] = $to;
                        }

                        if($account->isDirty())
                        {
                            $account->save();
                        }
                    }

                    // Advance the bar by the size of the chunked collection
                    $bar->advance($accounts->count());
                });


                $bar->finish();
            });

            $this->info("All media disks updated.");
            $this->info("Remember to re-upload your site icons.");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

    }
}
