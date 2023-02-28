<?php

namespace Inovector\Mixpost\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Contracts\Console\Kernel;
use Inovector\Mixpost\MixpostServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('login', fn() => 'Login Form')->name('login');

        Redis::flushAll();
        Cache::clear();

        Gate::define('viewMixpost', fn() => true);

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'Inovector\\Mixpost\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            MixpostServiceProvider::class,
        ];
    }

    protected function refreshTestDatabase()
    {
        if (!RefreshDatabaseState::$migrated) {
            $this->artisan('vendor:publish', ['--tag' => 'mixpost-migrations', '--force' => true])->run();
            $this->artisan('migrate:fresh', $this->migrateFreshUsing());

            $migration = include __DIR__ . '/../vendor/orchestra/testbench-core/laravel/migrations/2014_10_12_000000_testbench_create_users_table.php';
            $migration->up();

            $migration = include __DIR__ . '/../vendor/orchestra/testbench-core/laravel/migrations/2019_08_19_000000_testbench_create_failed_jobs_table.php';
            $migration->up();

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'mysql');

        config()->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3305'),
            'database' => env('DB_DATABASE', 'mixpost_test'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'prefix' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
    }

    public function processQueuedJobs()
    {
        foreach (Queue::pushedJobs() as $jobs) {
            foreach ($jobs as $job) {
                $job['job']->handle();
            }
        }
    }
}
