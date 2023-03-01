<?php

namespace Inovector\Mixpost\ServiceFormRules;

use Inovector\Mixpost\Contracts\ServiceFormRules;

class TwitterServiceFormRules implements ServiceFormRules
{
    static function form(): array
    {
        return [
            'client_id' => '',
            'client_secret' => ''
        ];
    }

    public static function rules(): array
    {
        return [
            'client_id' => ['required'],
            'client_secret' => ['required'],
        ];
    }

    public static function messages(): array
    {
        return [
            'client_id' => 'The API Key is required.',
            'client_secret' => 'The API Secret is required.'
        ];
    }
}
