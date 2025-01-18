<?php

namespace Inovector\Mixpost\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Inovector\Mixpost\Actions\AccountPublishPost;
use Inovector\Mixpost\Concerns\Job\HasSocialProviderJobRateLimit;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Post;

class AccountPublishPostJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use HasSocialProviderJobRateLimit;

    public $deleteWhenMissingModels = true;

    public Account $account;
    public Post $post;

    public function __construct(Account $account, Post $post)
    {
        $this->account = $account;
        $this->post = $post;
    }

    public function handle(AccountPublishPost $accountPublishPost): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        if ($this->post->isInHistory()) {
            return;
        }

        if (!$this->account->isServiceActive()) {
            $this->post->insertErrors($this->account, ['Service disabled']);
            return;
        }

        if ($this->account->isUnauthorized()) {
            $this->post->insertErrors($this->account, ['Access token expired']);

            return;
        }

        if ($retryAfter = $this->rateLimitExpiration()) {
            $this->release($retryAfter);

            return;
        }

        $response = $accountPublishPost($this->account, $this->post);

        if ($response->isUnauthorized()) {
            $this->account->setUnauthorized();
            $this->delete();

            return;
        }

        if ($response->hasExceededRateLimit()) {
            $this->storeRateLimitExceeded($response->retryAfter(), $response->isAppLevel());
            $this->release($response->retryAfter());

            return;
        }

        if ($response->rateLimitAboutToBeExceeded()) {
            $this->storeRateLimitExceeded($response->retryAfter(), $response->isAppLevel());
        }

        if ($response->hasError()) {
            // We are deleting this job from queue because all info about the failed post is in the `mixpost_post_accounts` table.
            $this->delete();
        }
    }
}
