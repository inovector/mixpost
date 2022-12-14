<?php

namespace Inovector\Mixpost\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Inovector\Mixpost\Actions\AccountPublishPost;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Post;

class AccountPublishPostJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    public Account $account;
    public Post $post;

    public function __construct(Account $account, Post $post)
    {
        $this->account = $account;
        $this->post = $post;
    }

    public function handle(AccountPublishPost $accountPublishPost)
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        if ($this->post->isPublished()) {
            return;
        }

        $accountPublishPost($this->account, $this->post);
    }
}
