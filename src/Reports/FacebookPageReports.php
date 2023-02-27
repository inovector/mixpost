<?php

namespace Inovector\Mixpost\Reports;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Inovector\Mixpost\Contracts\ProviderReports;
use Inovector\Mixpost\Enums\FacebookInsightType;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Audience;
use Inovector\Mixpost\Models\FacebookInsight;

class FacebookPageReports implements ProviderReports
{
    public function __invoke(Account $account, string $period): array
    {
        return [
            'metrics' => $this->metrics($account, $period),
            'audience' => $this->audience($account, $period)
        ];
    }

    protected function metrics(Account $account, string $period): array
    {
        $reports = FacebookInsight::account($account->id)
            ->select('type', DB::raw('SUM(value) as total'))
            ->when($period, function (Builder $query) use ($period) {
                return $this->queryPeriod($query, $period);
            })
            ->groupBy('type')
            ->get();

        return [
            'page_engaged_users' => $reports->where('type', FacebookInsightType::PAGE_ENGAGED_USERS)->value('total', 0),
            'page_post_engagements' => $reports->where('type', FacebookInsightType::PAGE_POST_ENGAGEMENTS)->value('total', 0),
            'page_posts_impressions' => $reports->where('type', FacebookInsightType::PAGE_POSTS_IMPRESSIONS)->value('total', 0)
        ];
    }

    protected function audience(Account $account, string $period): array
    {
        $report = Audience::account($account->id)
            ->select('date', DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->when($period, function (Builder $query) use ($period) {
                return $this->queryPeriod($query, $period);
            })->pluck('total', 'date');

        $period = match ($period) {
            '7_days' => CarbonPeriod::create(Carbon::now()->subDays(6), Carbon::now()),
            '30_days' => CarbonPeriod::create(Carbon::now()->subDays(29), Carbon::now()),
            '90_days' => CarbonPeriod::create(Carbon::now()->subDays(89), Carbon::now()),
        };

        $dataset = collect($period)->map(function ($item) use ($report) {
            $firstDate = $report->keys()->first();

            $total = intval($report[$item->toDateString()] ?? 0);

            return [
                'label' => $item->format('M j'),
                'value' => $firstDate ? ($item->toDateString() >= $firstDate ? $total : null) : null,
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
            '7_days' => $query->whereDate('date', '>=', Carbon::now()->subDays(7)->toDateString())
                ->whereDate('date', '<=', Carbon::now()->toDateString()),
            '30_days' => $query->whereDate('date', '>=', Carbon::now()->subDays(30)->toDateString())
                ->whereDate('date', '<=', Carbon::now()->toDateString()),
            '90_days' => $query->whereDate('date', '>=', Carbon::now()->subDays(90)->toDateString())
                ->whereDate('date', '<=', Carbon::now()->toDateString())
        };
    }
}
