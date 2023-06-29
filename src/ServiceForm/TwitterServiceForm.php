<?php

namespace Inovector\Mixpost\ServiceForm;

use Illuminate\Validation\Rule;
use Inovector\Mixpost\Abstracts\ServiceForm;

class TwitterServiceForm extends ServiceForm
{
    public static array $configs = ['tier'];

    static function form(): array
    {
        return [
            'client_id' => '',
            'client_secret' => '',
            'tier' => 'free'
        ];
    }

    public static function rules(): array
    {
        return [
            'client_id' => ['required'],
            'client_secret' => ['required'],
            'tier' => ['required', Rule::in(['legacy', 'free', 'basic'])]
        ];
    }

    public static function messages(): array
    {
        return [
            'client_id' => 'The API Key is required.',
            'client_secret' => 'The API Secret is required.',
            'tier' => 'Tier is invalid.'
        ];
    }
}
