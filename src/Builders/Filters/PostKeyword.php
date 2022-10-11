<?php

namespace Inovector\Mixpost\Builders\Filters;

use Illuminate\Database\Eloquent\Builder;
use Inovector\Mixpost\Contracts\Filter;

class PostKeyword implements Filter
{
    public static function apply(Builder $builder, $value): Builder
    {
        return $builder;
//        return $builder->whereHas('versions', function ($query) use ($value) {
//            $query->whereIn('id', Arr::wrap($value));
//        });
    }
}
