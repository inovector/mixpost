<?php

namespace Inovector\Mixpost\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Inovector\Mixpost\Settings schema()
 * @method static \Inovector\Mixpost\Settings get(string $name)
 * @method static \Inovector\Mixpost\Settings getFromCache(string $name, mixed $default = null)
 * @method static \Inovector\Mixpost\Settings all()
 * @method static \Inovector\Mixpost\Settings clearCache()
 * @method static \Inovector\Mixpost\Settings put(string $name, mixed $default = null)
 *
 * @see \Inovector\Mixpost\Settings
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Settings';
    }
}
