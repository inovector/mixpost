<?php

namespace Inovector\Mixpost\Concerns;

use Inovector\Mixpost\Models\User;

trait UsesUserModel
{
    public static function getUserClass(): string
    {
        return config('mixpost.user_model', User::class);
    }
}
