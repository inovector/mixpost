<?php

namespace Inovector\Mixpost;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Inovector\Mixpost\Commands\ClearServicesCache;
use Inovector\Mixpost\Commands\ClearSettingsCache;
use Inovector\Mixpost\Commands\CreateMastodonApp;
use Inovector\Mixpost\Commands\DeleteOldData;
use Inovector\Mixpost\Commands\ImportAccountAudience;
use Inovector\Mixpost\Commands\ImportAccountData;
use Inovector\Mixpost\Commands\ProcessMetrics;
use Inovector\Mixpost\Commands\PruneTemporaryDirectory;
use Inovector\Mixpost\Commands\PublishAssetsCommand;
use Inovector\Mixpost\Commands\RunScheduledPosts;
use Inovector\Mixpost\Events\AccountAdded;
use Inovector\Mixpost\Events\AccountUnauthorized;
use Inovector\Mixpost\Exceptions\MixpostExceptionHandler;
use Inovector\Mixpost\Listeners\HandleAccountImports;
use Inovector\Mixpost\Listeners\SendAccountUnauthorizedNotification;
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
                'create_mixpost_tables'
            ])
            ->hasCommands([
                PublishAssetsCommand::class,
                CreateMastodonApp::class,
                ClearSettingsCache::class,
                ClearServicesCache::class,
                RunScheduledPosts::class,
                ImportAccountAudience::class,
                ImportAccountData::class,
                ProcessMetrics::class,
                DeleteOldData::class,
                PruneTemporaryDirectory::class,
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
        $this->app->singleton('MixpostSocialProviderManager', function ($app) {
            return new SocialProviderManager($app);
        });

        $this->app->singleton('MixpostSettings', function ($app) {
            return new Settings($app);
        });

        $this->app->singleton('MixpostServiceManager', function ($app) {
            return new ServiceManager($app);
        });
    }

    public function packageBooted()
    {
        $this->bootEvents();

        $this->registerExceptionHandler();

        Gate::define('viewMixpost', function () {
            return true;
        });
    }

    protected function bootEvents(): void
    {
        Event::listen(AccountAdded::class, HandleAccountImports::class);
        Event::listen(AccountUnauthorized::class, SendAccountUnauthorizedNotification::class);
    }

    protected function registerExceptionHandler(): void
    {
        app()->bind(ExceptionHandler::class, MixpostExceptionHandler::class);
    }

    protected function writeSeparationLine(InstallCommand $command): void
    {
        $command->info('*---------------------------------------------------------------------------*');
    }
}
