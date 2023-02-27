<?php

namespace Inovector\Mixpost\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inovector\Mixpost\Enums\FacebookInsightType;
use Inovector\Mixpost\Facades\SocialProviderManager;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\FacebookInsight;
use Inovector\Mixpost\Support\Log;

class ImportFacebookInsightsJob implements ShouldQueue
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
        $insights = $this->getInsights();

        if (empty($insights)) {
            return;
        }

        foreach ($insights as $insight) {
            $this->importInsights(FacebookInsightType::fromName(Str::upper($insight['name'])), $insight['values']);
        }
    }

    protected function importInsights(FacebookInsightType $type, array $items)
    {
        $data = Arr::map($items, function ($item) use ($type) {
            return [
                'account_id' => $this->account->id,
                'type' => $type,
                'date' => Carbon::parse($item['end_time'])->toDateString(),
                'value' => $item['value'],
            ];
        });

        FacebookInsight::upsert($data, ['account_id', 'type', 'date'], ['value']);
    }

    protected function getInsights()
    {
        $connect = SocialProviderManager::connect($this->account->provider, $this->account->values())->useAccessToken($this->account->access_token->toArray());

        $result = $connect->getPageInsights();

        if (isset($result['error'])) {
            Log::error("{$this->job->getName()}: {$result['error']['desc']}", $this->job->payload());

            $this->delete();

            return false;
        }

        return $result['data'] ?? [];
    }
}
