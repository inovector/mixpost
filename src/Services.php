<?php

namespace Inovector\Mixpost;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Inovector\Mixpost\Models\Service;
use Inovector\Mixpost\ServiceFormRules\FacebookServiceFormRules;
use Inovector\Mixpost\ServiceFormRules\TenorServiceFormRules;
use Inovector\Mixpost\ServiceFormRules\TwitterServiceFormRules;
use Inovector\Mixpost\ServiceFormRules\UnsplashServiceFormRules;
use Inovector\Mixpost\Support\Log;

class Services
{
    protected mixed $config;

    public function __construct(Container $container)
    {
        $this->config = $container->make('config');
    }

    public function form(): array
    {
        return [
            'twitter' => TwitterServiceFormRules::form(),
            'facebook' => FacebookServiceFormRules::form(),
            'unsplash' => UnsplashServiceFormRules::form(),
            'tenor' => TenorServiceFormRules::form(),
        ];
    }

    public function rules(string $service): array
    {
        return [
            'twitter' => TwitterServiceFormRules::rules(),
            'facebook' => FacebookServiceFormRules::rules(),
            'unsplash' => UnsplashServiceFormRules::rules(),
            'tenor' => TenorServiceFormRules::rules(),
        ][$service];
    }

    public function messages(string $service): array
    {
        return [
            'twitter' => TwitterServiceFormRules::messages(),
            'facebook' => FacebookServiceFormRules::messages(),
            'unsplash' => UnsplashServiceFormRules::messages(),
            'tenor' => TenorServiceFormRules::messages(),
        ][$service];
    }

    public function put(string $name, array $value): void
    {
        Cache::put($this->resolveCacheKey($name), Crypt::encryptString(json_encode($value)));
    }

    public function get(string $name, null|string $credentialKey = null)
    {
        $value = $this->getFromCache($name, function () use ($name) {
            $dbRecord = Service::where('name', $name)->first();

            try {
                $payload = $dbRecord ? $dbRecord->credentials->toArray() : Arr::get($this->form(), $name, []);

                $this->put($name, $payload);

                return $payload;
            } catch (DecryptException $exception) {
                $this->logDecryptionError($name, $exception);

                return [];
            }
        });

        if (!is_array($value)) {
            try {
                $value = json_decode(Crypt::decryptString($value), true);
            } catch (DecryptException $exception) {
                $this->logDecryptionError($name, $exception);

                $value = [];
            }
        }

        if ($credentialKey) {
            return Arr::get($value, $credentialKey);
        }

        return $value;
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

    public function forget($name): void
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
        return $this->config->get('mixpost.cache_prefix') . ".services.$key";
    }

    private function logDecryptionError($name, DecryptException $exception): void
    {
        Log::error("The application key cannot decrypt the service credentials: {$exception->getMessage()}", [
            'name' => $name
        ]);
    }
}
