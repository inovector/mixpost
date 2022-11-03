<?php

namespace Inovector\Mixpost;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Inovector\Mixpost\Models\Setting;

class Settings
{
    protected mixed $config;

    public function __construct(Container $container)
    {
        $this->config = $container->make('config');
    }

    public function schema(): array
    {
        return [
            'timezone' => 'UTC',
            'date_format' => 'human',
            'time_format' => 12,
            'week_starts_on' => 1,
            'default_accounts' => [],
        ];
    }

    public function put(string $name, mixed $default = null): void
    {
        Cache::put($this->resolveCacheKey($name), $default);
    }

    public function get(string $name)
    {
        return $this->getFromCache($name, function () use ($name) {
            $dbRecord = Setting::where('name', $name)->first();

            $defaultPayload = $dbRecord ? $dbRecord->payload : $this->schema()[$name];

            $this->put($name, $defaultPayload);

            return $defaultPayload;
        });
    }

    public function all(): array
    {
        return Arr::map($this->schema(), function ($payload, $name) {
            return $this->get($name);
        });
    }

    public function getFromCache(string $name, mixed $default = null)
    {
        return Cache::get($this->resolveCacheKey($name), $default);
    }

    public function clearCache(): void
    {
        foreach ($this->schema() as $name => $payload) {
            Cache::forget($this->resolveCacheKey($name));
        }
    }

    private function resolveCacheKey(string $key): string
    {
        return $this->config->get('mixpost.cache_prefix') . ".settings.$key";
    }
}
