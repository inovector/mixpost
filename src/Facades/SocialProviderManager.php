<?php

namespace Lao9s\Mixpost\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Lao9s\Mixpost\Contracts\SocialProvider connect(string $provider)
 *
 * @see \Lao9s\Mixpost\Abstracts\SocialProviderManager
 */
class SocialProviderManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'SocialProviderManager';
    }
}
