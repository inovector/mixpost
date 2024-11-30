<?php

namespace Inovector\Mixpost\Concerns;

trait Makeable
{
    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }
}

