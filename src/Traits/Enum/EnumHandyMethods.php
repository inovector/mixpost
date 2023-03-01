<?php

namespace Inovector\Mixpost\Traits\Enum;

trait EnumHandyMethods
{
    public static function fromName(string $name)
    {
        return constant("self::$name");
    }
}
