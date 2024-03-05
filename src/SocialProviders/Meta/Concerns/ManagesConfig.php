<?php

namespace Inovector\Mixpost\SocialProviders\Meta\Concerns;

use Inovector\Mixpost\Facades\Services;
use Inovector\Mixpost\ServiceForm\FacebookServiceForm;

trait ManagesConfig
{
    public static function getApiVersionConfig(): string
    {
        $versions = FacebookServiceForm::versions();

        return Services::get('facebook', 'api_version') ?? current($versions);
    }
}
