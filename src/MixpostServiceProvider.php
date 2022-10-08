<?php

namespace Inovector\Mixpost;

use Illuminate\Support\Facades\Gate;
use Inovector\Mixpost\Commands\PublishAssetsCommand;
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
                'create_mixpost_accounts_table',
                'create_mixpost_posts_table',
                'create_mixpost_post_accounts_table',
                'create_mixpost_post_versions_table',
                'create_mixpost_tags_table',
                'create_mixpost_tag_post_table',
                'create_mixpost_media_table',
            ])
            ->hasCommand(PublishAssetsCommand::class);
    }

    public function register()
    {
        $this->app->singleton('SocialProviderManager', function ($app) {
            return new SocialProviderManager($app);
        });

        return parent::register();
    }

    public function boot()
    {
        Gate::define('viewMixpost', function () {
            return true;
        });

        return parent::boot();
    }
}
