<?php

namespace Inovector\Mixpost\ServiceForm;

use Inovector\Mixpost\Abstracts\ServiceForm;

class MeetupServiceForm extends ServiceForm
{
    static function form(): array
    {
        return [
            'client_id' => '',
            'client_secret' => '',
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
            'client_id' => 'The Client ID is required.',
            'client_secret' => 'The Client Secret is required.',
        ];
    }
}
