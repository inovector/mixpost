<?php

namespace Inovector\Mixpost\Services;

use Illuminate\Validation\Rule;
use Inovector\Mixpost\Abstracts\Service;
use Inovector\Mixpost\Enums\ServiceGroup;

class FacebookService extends Service
{
    public static function group(): ServiceGroup
    {
        return ServiceGroup::SOCIAL;
    }

    public static function versions(): array
    {
        return ['v24.0', 'v23.0', 'v22.0', 'v21.0', 'v20.0', 'v19.0', 'v18.0', 'v17.0', 'v16.0'];
    }

    static function form(): array
    {
        return [
            'client_id' => '',
            'client_secret' => '',
            'api_version' => current(self::versions())
        ];
    }

    public static function formRules(): array
    {
        return [
            "client_id" => ['required'],
            "client_secret" => ['required'],
            "api_version" => ['required', Rule::in(self::versions())],
        ];
    }

    public static function formMessages(): array
    {
        return [
            'client_id' => 'The App ID is required.',
            'client_secret' => 'The APP Secret is required.',
            'api_version' => 'The API Version is required.',
        ];
    }
}
