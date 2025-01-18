<?php

namespace Inovector\Mixpost\Services;

use Illuminate\Validation\Rule;
use Inovector\Mixpost\Abstracts\Service;
use Inovector\Mixpost\Enums\ServiceGroup;

class TwitterService extends Service
{
    public static array $exposedFormAttributes = ['tier'];

    public static function group(): ServiceGroup
    {
        return ServiceGroup::SOCIAL;
    }

    static function form(): array
    {
        return [
            'client_id' => '',
            'client_secret' => '',
            'tier' => 'free'
        ];
    }

    public static function formRules(): array
    {
        return [
            'client_id' => ['required'],
            'client_secret' => ['required'],
            'tier' => ['required', Rule::in(['legacy', 'free', 'basic'])]
        ];
    }

    public static function formMessages(): array
    {
        return [
            'client_id' => 'The API Key is required.',
            'client_secret' => 'The API Secret is required.',
            'tier' => 'Tier is invalid.'
        ];
    }
}
