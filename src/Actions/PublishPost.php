<?php

namespace Inovector\Mixpost\Actions;

use Illuminate\Support\Facades\Bus;
use Inovector\Mixpost\Jobs\AccountPublishPostJob;
use Inovector\Mixpost\Models\Post;

class PublishPost
{
    public function __invoke(Post $post): void
    {
        if ($post->isScheduleProcessing()) {
            return;
        }

        $post->setScheduleProcessing();

        $jobs = $post->accounts->map(function ($account) use ($post) {
            return new AccountPublishPostJob($account, $post);
        });

        Bus::batch($jobs)
            ->allowFailures()
            ->finally(function () use ($post) {
                $hasErrors = $post->accounts()->wherePivot('errors', '!=', null)->exists();

                if ($hasErrors) {
                    $post->setFailed();
                    return;
                }

                $post->setPublished();
            })
            ->dispatch();
    }
}
