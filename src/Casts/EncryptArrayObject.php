<?php

namespace Inovector\Mixpost\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Support\Facades\Crypt;

class EncryptArrayObject implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): ?ArrayObject
    {
        if (isset($attributes[$key])) {
            return new ArrayObject(json_decode(Crypt::decryptString($attributes[$key]), true));
        }

        return null;
    }

    public function set($model, string $key, $value, array $attributes): ?array
    {
        if (!is_null($value)) {
            return [$key => Crypt::encryptString(json_encode($value))];
        }

        return null;
    }
}
