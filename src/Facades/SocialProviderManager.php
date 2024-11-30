<?php

namespace Inovector\Mixpost\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Inovector\Mixpost\Contracts\SocialProvider connect(string $provider, array $values = [])
 * @method static \Inovector\Mixpost\Contracts\SocialProvider useAccessToken(array $token = [])
 * @method static array providers()
 *
 * @see \Inovector\Mixpost\Abstracts\SocialProviderManager
 */
class SocialProviderManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'MixpostSocialProviderManager';
    }
}
