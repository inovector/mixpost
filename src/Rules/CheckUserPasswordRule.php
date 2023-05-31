<?php

namespace Inovector\Mixpost\Rules;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class CheckUserPasswordRule implements Rule
{
    public $user;
    public ?string $message;

    public function __construct($user, ?string $message = null)
    {
        $this->user = $user;
        $this->message = $message;
    }

    public function passes($attribute, $value): bool
    {
        return Hash::check($value, $this->user->password);
    }

    public function message(): array|string|Translator|Application|null
    {
        return $this->message ?: trans('validation.password');
    }
}
