<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inovector\Mixpost\Concerns\UsesAuth;
use Inovector\Mixpost\Concerns\UsesUserModel;
use Inovector\Mixpost\Rules\CheckUserPasswordRule;

class UpdateAuthUserPassword extends FormRequest
{
    use UsesAuth;
    use UsesUserModel;

    protected $user;

    public function rules(): array
    {
        $this->user = self::getUserClass()::findOrFail(self::getAuthGuard()->user()->id);

        return [
            'current_password' => ['required', new CheckUserPasswordRule($this->user, 'The current password field confirmation does not match.')],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    public function handle(): void
    {
        $this->user->update([
            'password' => Hash::make($this->input('password')),
        ]);
    }
}
