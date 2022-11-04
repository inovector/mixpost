<?php

namespace Inovector\Mixpost\Builders\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inovector\Mixpost\Contracts\Filter;

class PostKeyword implements Filter
{
    public static function apply(Builder $builder, $value): Builder
    {
        return $builder->whereHas('versions', function ($query) use ($value) {
            $query->whereRaw(DB::raw('LOWER(content->>"$[*].body") LIKE  "%' . Str::lower($value) . '%"'));
        });
    }
}
