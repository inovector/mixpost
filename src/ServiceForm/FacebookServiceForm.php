<?php

namespace Inovector\Mixpost\ServiceForm;

use Illuminate\Validation\Rule;
use Inovector\Mixpost\Abstracts\ServiceForm;

class FacebookServiceForm extends ServiceForm
{
    public static function versions(): array
    {
        return ['v20.0', 'v19.0', 'v18.0', 'v17.0', 'v16.0'];
    }

    static function form(): array
    {
        return [
            'client_id' => '',
            'client_secret' => '',
            'api_version' => current(self::versions())
        ];
    }

    public static function rules(): array
    {
        return [
            "client_id" => ['required'],
            "client_secret" => ['required'],
            "api_version" => ['required', Rule::in(self::versions())],
        ];
    }

    public static function messages(): array
    {
        return [
            'client_id' => 'The App ID is required.',
            'client_secret' => 'The APP Secret is required.',
            'api_version' => 'The API Version is required.',
        ];
    }
}
