<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Inovector\Mixpost\Actions\PublishPost;
use Inovector\Mixpost\Enums\PostScheduleStatus;
use Inovector\Mixpost\Enums\PostStatus;
use Inovector\Mixpost\Models\Post;

class RunScheduledPosts extends Command
{
    public $signature = 'mixpost:run-scheduled-posts';

    public $description = 'Scan & run scheduled posts';

    public function handle(): int
    {
        Post::with('accounts')
            ->where('status', PostStatus::SCHEDULED->value)
            ->where('schedule_status', PostScheduleStatus::PENDING->value)
            ->where('scheduled_at', '<=', now()->tz('UTC'))
            ->each(function (Post $post) {
                (new PublishPost())($post);
            });

        return self::SUCCESS;
    }
}
