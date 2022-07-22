<?php

namespace Lao9s\Mixpost\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Lao9s\Mixpost\Mixpost
 */
class Mixpost extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mixpost';
    }
}
