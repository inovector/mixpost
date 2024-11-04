<?php

namespace Inovector\Mixpost\SocialProviders\Twitter\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\ImportedPost;
use Inovector\Mixpost\Models\Metric;

class ProcessTwitterMetricsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    public Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function handle()
    {
        $items = ImportedPost::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.likes")) as likes'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.replies")) as replies'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.retweets")) as retweets'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.impressions")) as impressions'))
            ->where('account_id', $this->account->id)
            ->groupBy('date')
            ->cursor();

        $data = $items->map(function ($item) {
            return [
                'account_id' => $this->account->id,
                'date' => $item->date,
                'data' => json_encode([
                    'likes' => $item->likes,
                    'replies' => $item->replies,
                    'retweets' => $item->retweets,
                    'impressions' => $item->impressions,
                ])
            ];
        });

        Metric::upsert($data->toArray(), ['data'], ['account_id', 'date']);
    }
}
