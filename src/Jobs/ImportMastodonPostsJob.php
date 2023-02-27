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
use Inovector\Mixpost\Facades\SocialProviderManager;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\ImportedPost;
use Inovector\Mixpost\Support\Log;

class ImportMastodonPostsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    public Account $account;
    public array $params;

    public function __construct(Account $account, array $params = [])
    {
        $this->account = $account;
        $this->params = $params;
    }

    public function handle()
    {
        $result = $this->getPosts();

        if (!$result) {
            return;
        }

        $this->import($result);

        // If more than 40(limit of page items), there is a probability that there are others.
        if (count($result) >= 40) {
            ImportMastodonPostsJob::dispatch($this->account, ['max_id' => Arr::last($result)['id']])->delay(60);
        }
    }

    protected function getPosts()
    {
        $connect = SocialProviderManager::connect($this->account->provider, $this->account->values())->useAccessToken($this->account->access_token->toArray());

        $result = $connect->getAccountStatuses($this->account->provider_id, $this->params['max_id'] ?? '');

        if (isset($result['error'])) {
            Log::error("{$this->job->getName()}: {$result['error']['desc']}", $this->job->payload());

            $this->delete();

            return false;
        }

        return Arr::where($result, function ($item) {
            return Carbon::parse($item['created_at'])->greaterThan(Carbon::now()->subDays(90));
        });
    }

    protected function import(array $items): void
    {
        $data = Arr::map($items, function ($item) {
            return [
                'account_id' => $this->account->id,
                'provider_post_id' => $item['id'],
                'content' => json_encode(['text' => $item['content']]),
                'metrics' => json_encode([
                    'replies' => $item['replies_count'],
                    'reblogs' => $item['reblogs_count'],
                    'favourites' => $item['favourites_count'],
                ]),
                'created_at' => Carbon::parse($item['created_at'])->toDateString()
            ];
        });

        ImportedPost::upsert($data, ['account_id', 'provider_post_id'], ['content', 'metrics']);
    }
}
