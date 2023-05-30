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
use Inovector\Mixpost\Concerns\Job\HasSocialProviderJobRateLimit;
use Inovector\Mixpost\Concerns\Job\SocialProviderJobFail;
use Inovector\Mixpost\Concerns\UsesSocialProviderManager;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\ImportedPost;
use Inovector\Mixpost\SocialProviders\Twitter\TwitterProvider;

class ImportTwitterPostsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use UsesSocialProviderManager;
    use HasSocialProviderJobRateLimit;
    use SocialProviderJobFail;

    public $deleteWhenMissingModels = true;

    public Account $account;
    public array $params;

    public function __construct(Account $account, array $params = [])
    {
        $this->account = $account;
        $this->params = $params;
    }

    public function handle(): void
    {
        if ($retryAfter = $this->rateLimitExpiration()) {
            $this->release($retryAfter);

            return;
        }

        /**
         * @var TwitterProvider $provider
         */
        $provider = $this->connectProvider($this->account);

        // Twitter `free` Tier doesn't support endpoints from this job
        if ($provider->getTier() === 'free') {
            return;
        }

        $response = $provider->getUserTweetTimeline($this->account->provider_id, $this->params['pagination_next_token'] ?? '');

        if ($response->hasExceededRateLimit()) {
            $this->storeRateLimitExceeded($response->retryAfter(), $response->isAppLevel());
            $this->release($response->retryAfter());

            return;
        }

        if ($response->rateLimitAboutToBeExceeded()) {
            $this->storeRateLimitExceeded($response->retryAfter(), $response->isAppLevel());
        }

        if ($response->hasError()) {
            $this->makeFail($response);

            return;
        }

        $this->import($response->data);

        if (isset($response->meta->next_token)) {
            ImportTwitterPostsJob::dispatch($this->account, ['pagination_next_token' => $response->meta->next_token])->delay(60);
        }
    }

    protected function import(array $items): void
    {
        $data = Arr::map($items, function ($item) {
            return [
                'account_id' => $this->account->id,
                'provider_post_id' => $item->id,
                'content' => json_encode(['text' => $item->text]),
                'metrics' => json_encode([
                    'user_profile_clicks' => $item->public_metrics->user_profile_clicks ?? 0,
                    'impressions' => $item->public_metrics->impression_count ?? 0,
                    'likes' => $item->public_metrics->like_count ?? 0,
                    'replies' => $item->public_metrics->reply_count ?? 0,
                    'retweets' => $item->public_metrics->retweet_count ?? 0,
                ]),
                'created_at' => Carbon::parse($item->created_at, 'UTC')->toDateString()
            ];
        });

        ImportedPost::upsert($data, ['account_id', 'provider_post_id'], ['content', 'metrics']);
    }
}
