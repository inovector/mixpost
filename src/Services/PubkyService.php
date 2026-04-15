<?php

namespace Inovector\Mixpost\Services;

use Inovector\Mixpost\Abstracts\Service;
use Inovector\Mixpost\Enums\ServiceGroup;

class PubkyService extends Service
{
    public static function group(): ServiceGroup
    {
        return ServiceGroup::SOCIAL;
    }

    public static function name(): string
    {
        return 'pubky';
    }

    public static function form(): array
    {
        return [
            'homeserver' => '',
        ];
    }

    public static function formRules(): array
    {
        return [
            'homeserver' => ['required', 'url'],
        ];
    }

    public static function formMessages(): array
    {
        return [
            'homeserver.required' => 'The homeserver URL is required.',
            'homeserver.url' => 'Please provide a valid homeserver URL.',
        ];
    }
}