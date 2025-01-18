<?php

namespace Inovector\Mixpost;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Inovector\Mixpost\Models\Setting;

class Settings
{
    protected mixed $config;

    public function __construct(Container $container)
    {
        $this->config = $container->make('config');
    }

    public function form(): array
    {
        return [
            'timezone' => 'UTC',
            'date_format' => 'human',
            'time_format' => 12,
            'week_starts_on' => 1,
            'admin_email' => '',
            'default_accounts' => [],
        ];
    }

    public function rules(): array
    {
        return [
            'timezone' => ['required', 'timezone'],
            'time_format' => ['required', Rule::in([12, 24])],
            'week_starts_on' => ['required', Rule::in([0, 1])],
            'admin_email' => ['required', 'email'],
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

            $defaultPayload = $dbRecord ? $dbRecord->payload : $this->form()[$name];

            $this->put($name, $defaultPayload);

            return $defaultPayload;
        });
    }

    public function all(): array
    {
        return Arr::map($this->form(), function ($payload, $name) {
            return $this->get($name);
        });
    }

    public function getFromCache(string $name, mixed $default = null)
    {
        return Cache::get($this->resolveCacheKey($name), $default);
    }

    public function forget(string $name): void
    {
        Cache::forget($this->resolveCacheKey($name));
    }

    public function forgetAll(): void
    {
        foreach ($this->form() as $name => $payload) {
            $this->forget($name);
        }
    }

    private function resolveCacheKey(string $key): string
    {
        return $this->config->get('mixpost.cache_prefix') . ".settings.$key";
    }
}
