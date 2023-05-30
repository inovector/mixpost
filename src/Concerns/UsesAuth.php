<?php

namespace Inovector\Mixpost\Concerns;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;

trait UsesAuth
{
    public static function getAuthGuard(): Guard|StatefulGuard
    {
        return Auth::guard(self::getAuthGuardName());
    }

    public static function getAuthGuardName(): string|null
    {
        return config('mixpost.auth_guard');
    }
}
