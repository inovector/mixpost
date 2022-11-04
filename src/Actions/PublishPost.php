<?php

namespace Inovector\Mixpost\Actions;

use Illuminate\Support\Facades\Bus;
use Inovector\Mixpost\Jobs\AccountPublishPostJob;
use Inovector\Mixpost\Models\Post;

class PublishPost
{
    public function __invoke(Post $post): void
    {
        $post->setPublishing();

        $jobs = $post->accounts->map(function ($account) use ($post) {
            return new AccountPublishPostJob($account, $post);
        });

        Bus::batch($jobs)
            ->allowFailures()
            ->finally(function () use ($post) {
                $accountsWithErrors = $post->accounts()->wherePivot('errors', '!=', null)->count();

                if ($post->accounts()->count() === $accountsWithErrors) {
                    $post->setError();
                    return;
                }

                $post->setPublished();
            })
            ->dispatch();
    }
}
