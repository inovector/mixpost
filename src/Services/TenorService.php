<?php

namespace Inovector\Mixpost\Services;

use Inovector\Mixpost\Abstracts\Service;
use Inovector\Mixpost\Enums\ServiceGroup;

class TenorService extends Service
{
    public static function group(): ServiceGroup
    {
        return ServiceGroup::MEDIA;
    }

    static function form(): array
    {
        return [
            'client_id' => ''
        ];
    }

    public static function formRules(): array
    {
        return [
            "client_id" => ['required']
        ];
    }

    public static function formMessages(): array
    {
        return [
            'client_id' => 'The API Key is required.'
        ];
    }
}
