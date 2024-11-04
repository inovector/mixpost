<?php

namespace Inovector\Mixpost\Contracts;

use Inovector\Mixpost\Enums\ServiceGroup;

interface Service
{
    public static function group(): ServiceGroup;

    public static function name(): string;

    public static function nameLocalized(): string;

    public static function form(): array;

    public static function formRules(): array;

    public static function formMessages(): array;
}
