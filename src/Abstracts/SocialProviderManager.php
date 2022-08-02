<?php

namespace Lao9s\Mixpost\Abstracts;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use InvalidArgumentException;

abstract class SocialProviderManager
{
    protected Container $container;
    protected mixed $config;
    protected array $providers = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->config = $container->make('config');
    }

    public function connect(string $provider)
    {
        // If the given provider has not been created before, we will create the instances
        // here and cache it, so we can return it next time very quickly. If there is
        // already a provider created by this name, we'll just return that instance.
        if (!isset($this->providers[$provider])) {
            $this->providers[$provider] = $this->createConnection($provider);
        }

        return $this->providers[$provider];
    }

    private function createConnection(string $provider)
    {
        $method = 'connect' . Str::studly($provider) . 'Provider';

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new InvalidArgumentException("Provider [$provider] not supported.");
    }

    protected function buildConnectionProvider($provider, $config)
    {
        return new $provider($this->container->make('request'), $config['client_id'], $config['client_secret'], $config['redirect']);
    }
}
