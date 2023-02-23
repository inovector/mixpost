<?php

namespace Inovector\Mixpost\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Inovector\Mixpost\Facades\SocialProviderManager;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\ImportedPost;
use Inovector\Mixpost\Support\Log;

class ImportTwitterPostsJob implements ShouldQueue
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
        $result = $this->getTwitterPosts();

        if (!$result) {
            return;
        }

        $this->import($result['data']);

        if (isset($result['meta']->next_token)) {
            ImportTwitterPostsJob::dispatch($this->account, ['pagination_next_token' => $result['meta']->next_token])->delay(60);
        }
    }

    protected function getTwitterPosts()
    {
        $connect = SocialProviderManager::connect($this->account->provider, $this->account->values())->useAccessToken($this->account->access_token->toArray());

        $result = $connect->getUserTweetTimeline($this->account->provider_id, $this->params['pagination_next_token'] ?? '');

        if (isset($result['error'])) {
            Log::error("{$this->job->getName()}: {$result['error']['desc']}", $this->job->payload());

            $this->delete();

            return false;
        }

        return $result;
    }

    protected function import(array $items): void
    {
        foreach ($items as $item) {
            ImportedPost::updateOrCreate([
                'account_id' => $this->account->id,
                'provider_post_id' => $item->id,
            ], [
                'content' => ['text' => $item->text],
                'metrics' => [
                    'user_profile_clicks' => $item->public_metrics->user_profile_clicks ?? 0,
                    'impressions' => $item->public_metrics->impression_count ?? 0,
                    'likes' => $item->public_metrics->like_count ?? 0,
                    'replies' => $item->public_metrics->reply_count ?? 0,
                    'retweets' => $item->public_metrics->retweet_count ?? 0,
                ],
                'created_at' => $item->created_at
            ]);
        }
    }
}
