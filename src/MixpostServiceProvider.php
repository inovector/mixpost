<?php

namespace Inovector\Mixpost;

use Illuminate\Support\Facades\Gate;
use Inovector\Mixpost\Commands\ClearSettingsCache;
use Inovector\Mixpost\Commands\CreateMastodonApp;
use Inovector\Mixpost\Commands\DeleteOldData;
use Inovector\Mixpost\Commands\ImportAccountAudience;
use Inovector\Mixpost\Commands\ProcessMetrics;
use Inovector\Mixpost\Commands\PublishAssetsCommand;
use Inovector\Mixpost\Commands\ImportAccountData;
use Inovector\Mixpost\Commands\RunScheduledPosts;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MixpostServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('mixpost')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web')
            ->hasMigrations([
                'create_mixpost_services_table',
                'create_mixpost_accounts_table',
                'create_mixpost_posts_table',
                'create_mixpost_post_accounts_table',
                'create_mixpost_post_versions_table',
                'create_mixpost_tags_table',
                'create_mixpost_tag_post_table',
                'create_mixpost_media_table',
                'create_mixpost_settings_table',
                'create_mixpost_imported_posts_table',
                'create_mixpost_facebook_insights_table',
                'create_mixpost_metrics_table',
                'create_mixpost_audience_table',
            ])
            ->hasCommands([
                PublishAssetsCommand::class,
                CreateMastodonApp::class,
                ClearSettingsCache::class,
                RunScheduledPosts::class,
                ImportAccountAudience::class,
                ImportAccountData::class,
                ProcessMetrics::class,
                DeleteOldData::class
            ])->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $this->writeSeparationLine($command);
                        $command->line('Mixpost Lite Installation. Self-hosted social media management software.');
                        $command->line('Laravel version: ' . app()->version());
                        $command->line('PHP version: ' . trim(phpversion()));
                        $command->line(' ');
                        $command->line('Github: https://github.com/inovector/mixpost');
                        $this->writeSeparationLine($command);
                        $command->line('');

                        $command->comment('Publishing assets');
                        $command->call('mixpost:publish-assets');
                    })
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('inovector/mixpost')
                    ->endWith(function (InstallCommand $command) {
                        $appUrl = config('app.url');

                        $command->line("Visit the Mixpost UI at $appUrl/mixpost");
                    });
            });
    }

    public function packageRegistered()
    {
        $this->app->singleton('SocialProviderManager', function ($app) {
            return new SocialProviderManager($app);
        });

        $this->app->singleton('Settings', function ($app) {
            return new Settings($app);
        });

        $this->app->singleton('Services', function ($app) {
            return new Services($app);
        });
    }

    public function packageBooted()
    {
        Gate::define('viewMixpost', function () {
            return true;
        });
    }

    protected function writeSeparationLine(InstallCommand $command): void
    {
        $command->info('*---------------------------------------------------------------------------*');
    }
}
