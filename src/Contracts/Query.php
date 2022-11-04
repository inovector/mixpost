<?php

namespace Inovector\Mixpost\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface Query
{
    public static function apply(Request $request): Builder;
}
