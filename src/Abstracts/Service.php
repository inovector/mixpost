<?php

namespace Inovector\Mixpost\Abstracts;

use Illuminate\Support\Str;
use Inovector\Mixpost\Contracts\Service as ServiceContract;
use Inovector\Mixpost\Enums\ServiceGroup;
use Inovector\Mixpost\Facades\ServiceManager;

abstract class Service implements ServiceContract
{
    /**
     * The form attributes that should be considered as exposed.
     * By default, all form attributes are encrypted and stored in the `configuration` column of database.
     * Exposed attributes are not sensitive and can be exposed to the user.
     * @return array
     * @var array
     */
    public static array $exposedFormAttributes = [];

    /**
     * Group name of the service.
     * Should be one of the values from ServiceGroup enum.
     * @return ServiceGroup
     */
    public static function group(): ServiceGroup
    {
        return ServiceGroup::MISCELLANEOUS;
    }

    /**
     * Unique name of the service.
     * Should be lowercase and snake cased.
     * @return string
     */
    public static function name(): string
    {
        $className = basename(str_replace('\\', '/', static::class));

        return Str::of($className)->replace('Service', '')->snake();
    }

    /**
     * Localized name of the service.
     * Friendly name for the user interface.
     * @return string
     */
    public static function nameLocalized(): string
    {
        $className = basename(str_replace('\\', '/', static::class));

        return Str::of($className)->replace('Service', '');
    }

    public static function getConfiguration(string $key = null)
    {
        if ($key) {
            return ServiceManager::get(static::name(), "configuration.$key");
        }

        return ServiceManager::get(static::name(), 'configuration');
    }

    public static function isActive(): bool
    {
        return ServiceManager::isActive(static::name());
    }
}
