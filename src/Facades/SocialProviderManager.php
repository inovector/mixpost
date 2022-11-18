<?php

namespace Inovector\Mixpost\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Inovector\Mixpost\Contracts\SocialProvider connect(string $provider)
 * @method static \Inovector\Mixpost\Contracts\SocialProvider useAccessToken(array $token = [])
 *
 * @see \Inovector\Mixpost\Abstracts\SocialProviderManager
 */
class SocialProviderManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'SocialProviderManager';
    }
}
