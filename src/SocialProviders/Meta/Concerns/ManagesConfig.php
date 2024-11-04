<?php

namespace Inovector\Mixpost\SocialProviders\Meta\Concerns;

use Inovector\Mixpost\Facades\ServiceManager;
use Inovector\Mixpost\Services\FacebookService;

trait ManagesConfig
{
    public static function getApiVersionConfig(): string
    {
        $versions = FacebookService::versions();

        return ServiceManager::get('facebook', 'api_version') ?? current($versions);
    }
}
