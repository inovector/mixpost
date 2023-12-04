<?php

namespace Inovector\Mixpost;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Inovector\Mixpost\Abstracts\ServiceForm;
use Inovector\Mixpost\Models\Service;
use Inovector\Mixpost\ServiceForm\FacebookServiceForm;
use Inovector\Mixpost\ServiceForm\MeetupServiceForm;
use Inovector\Mixpost\ServiceForm\TenorServiceForm;
use Inovector\Mixpost\ServiceForm\TwitterServiceForm;
use Inovector\Mixpost\ServiceForm\UnsplashServiceForm;
use Inovector\Mixpost\Support\Log;

class Services
{
    protected mixed $config;

    public function __construct(Container $container)
    {
        $this->config = $container->make('config');
    }

    public function services(?string $name = null): array|string|null
    {
        $services = [
            'facebook' => FacebookServiceForm::class,
            'twitter' => TwitterServiceForm::class,
            'unsplash' => UnsplashServiceForm::class,
            'tenor' => TenorServiceForm::class,
            'meetup' => MeetupServiceForm::class,
        ];

        foreach ($services as $service) {
            if (!app($service) instanceof ServiceForm) {
                throw new \Exception("The `$service` must be an instance of Abstracts\ServiceForm");
            }
        }

        if ($name) {
            return $services[$name] ?? null;
        }

        return $services;
    }

    // TODO - implement `isActive` instead `isConfigured`
    public function isConfigured(?string $service = null): array|bool
    {
        $list = Arr::map($this->services(), function ($_, $provider) {
            return !!$this->get($provider, 'client_id');
        });

        if ($service) {
            return $list[$service];
        }

        return $list;
    }

    public function put(string $name, array $value): void
    {
        Cache::put($this->resolveCacheKey($name), Crypt::encryptString(json_encode($value)));
    }

    public function get(string $name, null|string $credentialKey = null)
    {
        $serviceClass = $this->services($name);
        $defaultPayload = $serviceClass ? $serviceClass::form() : [];

        $value = $this->getFromCache($name, function () use ($name, $defaultPayload) {
            $dbRecord = Service::where('name', $name)->first();

            try {
                $payload = $dbRecord ? array_merge($defaultPayload, $dbRecord->credentials->toArray()) : $defaultPayload;

                $this->put($name, $payload);

                return $payload;
            } catch (DecryptException $exception) {
                $this->logDecryptionError($name, $exception);

                return $defaultPayload;
            }
        });

        if (!is_array($value)) {
            try {
                $value = json_decode(Crypt::decryptString($value), true);
            } catch (DecryptException $exception) {
                $this->logDecryptionError($name, $exception);

                $value = $defaultPayload;
            }
        }

        if ($credentialKey) {
            return Arr::get($value, $credentialKey);
        }

        return $value;
    }

    public function all(): array
    {
        return Arr::map($this->services(), function ($payload, $name) {
            return $this->get($name);
        });
    }

    public function configs(): array
    {
        return Arr::map($this->all(), function ($payload, $name) {
            return Arr::only($payload, $this->services($name)::$configs);
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
        foreach (array_keys($this->services()) as $name) {
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
