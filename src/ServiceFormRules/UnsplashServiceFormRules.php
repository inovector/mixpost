<?php

namespace Inovector\Mixpost\ServiceFormRules;

use Inovector\Mixpost\Contracts\ServiceFormRules;

class UnsplashServiceFormRules implements ServiceFormRules
{
    static function form(): array
    {
        return [
            'client_id' => ''
        ];
    }

    public static function rules(): array
    {
        return [
            "client_id" => ['required']
        ];
    }

    public static function messages(): array
    {
        return [
            'client_id' => 'The API Key is required.'
        ];
    }
}
