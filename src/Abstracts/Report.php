<?php

namespace Inovector\Mixpost\Abstracts;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Inovector\Mixpost\Contracts\ProviderReports;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Audience;

abstract class Report implements ProviderReports
{
    protected function audience(Account $account, string $period): array
    {
        $report = Audience::account($account->id)
            ->select('date', DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->when($period, function (Builder $query) use ($period) {
                return $this->queryPeriod($query, $period);
            })
            ->orderBy('date', 'asc')
            ->pluck('total', 'date');

        $period = match ($period) {
            '7_days' => CarbonPeriod::create(Carbon::now('UTC')->subDays(6), Carbon::now('UTC')),
            '30_days' => CarbonPeriod::create(Carbon::now('UTC')->subDays(29), Carbon::now('UTC')),
            '90_days' => CarbonPeriod::create(Carbon::now('UTC')->subDays(89), Carbon::now('UTC')),
        };

        $dataset = collect($period)->map(function ($item) use ($report) {
            $firstDate = $report->keys()->first();

            $total = Arr::get($report, $item->toDateString());
            $value = $total !== null ? intval($total) : null;

            return [
                'label' => $item->format('M j'),
                'value' => $firstDate ? ($item->toDateString() >= $firstDate ? $value : null) : null,
            ];
        });

        return [
            'labels' => $dataset->pluck('label'),
            'values' => $dataset->pluck('value'),
        ];
    }

    protected function queryPeriod(Builder $query, string $period): Builder
    {
        return match ($period) {
            '7_days' => $query->whereDate('date', '>=', Carbon::today('UTC')->subDays(7)->toDateString())
                ->whereDate('date', '<=', Carbon::today('UTC')->toDateString()),
            '30_days' => $query->whereDate('date', '>=', Carbon::today('UTC')->subDays(30)->toDateString())
                ->whereDate('date', '<=', Carbon::today('UTC')->toDateString()),
            '90_days' => $query->whereDate('date', '>=', Carbon::today('UTC')->subDays(90)->toDateString())
                ->whereDate('date', '<=', Carbon::today('UTC')->toDateString())
        };
    }
}
