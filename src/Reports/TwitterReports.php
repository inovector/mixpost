<?php

namespace Inovector\Mixpost\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Inovector\Mixpost\Abstracts\Report;
use Inovector\Mixpost\Facades\ServiceManager;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Metric;

class TwitterReports extends Report
{
    public function __invoke(Account $account, string $period): array
    {
        return [
            'metrics' => $this->metrics($account, $period),
            'audience' => $this->audience($account, $period),
            'tier' => ServiceManager::get('twitter', 'tier') ?? 'legacy'
        ];
    }

    protected function metrics(Account $account, string $period): array
    {
        $report = Metric::account($account->id)->select(
            DB::raw('SUM(JSON_EXTRACT(data, "$.likes")) as likes'),
            DB::raw('SUM(JSON_EXTRACT(data, "$.retweets")) as retweets'),
            DB::raw('SUM(JSON_EXTRACT(data, "$.impressions")) as impressions')
        )->when($period, function (Builder $query) use ($period) {
            return $this->queryPeriod($query, $period);
        })->first();

        return [
            'likes' => $report->likes ?? 0,
            'retweets' => $report->retweets ?? 0,
            'impressions' => $report->impressions ?? 0,
        ];
    }
}
