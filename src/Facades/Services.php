<?php

namespace Inovector\Mixpost\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array services(?string $name = null)
 * @method static get(string $name, null|string $credentialKey = null)
 * @method static getFromCache(string $name)
 * @method static array all()
 * @method static array configs()
 * @method static isConfigured(?string $service = null)
 * @method static void forgetAll()
 * @method static void put(string $name, array $value)
 * @method static void forget(string $name)
 *
 * @see \Inovector\Mixpost\Services
 */
class Services extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'MixpostServices';
    }
}
