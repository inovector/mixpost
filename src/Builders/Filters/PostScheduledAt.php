<?php

namespace Inovector\Mixpost\Builders\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Inovector\Mixpost\Contracts\Filter;

class PostScheduledAt implements Filter
{
    public static function apply(Builder $builder, $value): Builder
    {
        $date = Carbon::parse($value['date']);

        if ($value['calendar_type'] === 'month') {
            return $builder->whereDate('scheduled_at', '>=', $date->clone()->startOfMonth()->subDays(10)->toDateString())
                ->whereDate('scheduled_at', '<=', $date->clone()->endOfMonth()->addDays(10)->toDateString());
        }

        if ($value['calendar_type'] === 'week') {
            return $builder->whereDate('scheduled_at', '>=', $date->startOfWeek()->toDateString())
                ->whereDate('scheduled_at', '<=', $date->endOfWeek()->toDateString());
        }

        if ($value['calendar_type'] === 'day') {
            return $builder->whereDate('scheduled_at', $date->toDateString());
        }

        return $builder;
    }
}
