<?php

namespace Inovector\Mixpost\Abstracts;

use Inovector\Mixpost\Contracts\SocialProvider;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use InvalidArgumentException;

abstract class SocialProviderManager
{
    protected Container $container;
    protected mixed $config;
    protected mixed $values = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->config = $container->make('config');
    }

    public function connect(string $provider, array $values = [])
    {
        $this->setValues($values);

        return $this->createConnection($provider);
    }

    protected function setValues(array $values): void
    {
        $this->values = $values;
    }

    private function createConnection(string $provider)
    {
        $method = 'connect' . Str::studly($provider) . 'Provider';

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new InvalidArgumentException("Provider [$provider] not supported.");
    }

    protected function buildConnectionProvider(string $provider, array $config): SocialProvider
    {
        $connection = (new $provider($this->container->make('request'), $config['client_id'], $config['client_secret'], $config['redirect'], array_merge($this->values, $config['values'] ?? [])));

        if (!$connection instanceof SocialProvider) {
            throw new \Exception('The provider must be an instance of SocialProvider');
        }

        return $connection;
    }
}
