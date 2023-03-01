<?php

namespace Inovector\Mixpost\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Inovector\Mixpost\Facades\SocialProviderManager;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Audience;
use Inovector\Mixpost\Support\Log;

class ImportMastodonFollowersJob implements ShouldQueue
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
        $total = $this->getFollowersCount();

        if ($total === false) {
            return;
        }

        Audience::updateOrCreate([
            'account_id' => $this->account->id,
            'date' => Carbon::now()->toDateString()
        ], [
            'total' => $total
        ]);
    }

    protected function getFollowersCount()
    {
        $connect = SocialProviderManager::connect($this->account->provider, $this->account->values())->useAccessToken($this->account->access_token->toArray());

        $result = $connect->getAccountMetrics();

        if (isset($result['error'])) {
            Log::error("{$this->job->getName()}: {$result['error']['desc']}", $this->job->payload());

            $this->delete();

            return false;
        }

        return $result['followers_count'] ?? 0;
    }
}
