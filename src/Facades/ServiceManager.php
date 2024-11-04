<?php

namespace Inovector\Mixpost\Facades;

use Illuminate\Support\Facades\Facade;
use Inovector\Mixpost\Collection\ServiceCollection;

/**
 * @method static ServiceCollection services()
 * @method static string|null getServiceClass(string $name)
 * @method static get(string $name, null|string $key = null)
 * @method static getFromCache(string $name)
 * @method static array all()
 * @method static array|bool isActive(string|array $name = null)
 * @method static array|bool isConfigured(string|array $name = null)
 * @method static array exposedConfiguration(string|array $name = null)
 * @method static void forgetAll()
 * @method static void put(string $name, array $configuration, bool $active = false)
 * @method static void forget(string $name)
 * @method static void retrievalAction(callable $action)
 *
 * @see \Inovector\Mixpost\ServiceManager
 */
class ServiceManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'MixpostServiceManager';
    }
}
