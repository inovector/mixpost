<?php

namespace Inovector\Mixpost\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array form()
 * @method static array rules(string $name)
 * @method static array messages(string $name)
 * @method static get(string $name, null|string $credentialKey = null)
 * @method static getFromCache(string $name)
 * @method static array all()
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
