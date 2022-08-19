<?php

namespace Inovector\Mixpost\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Inovector\Mixpost\MixpostServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Inovector\\Mixpost\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            MixpostServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_mixpost_table.php.stub';
        $migration->up();
        */
    }
}
