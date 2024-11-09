<?php

namespace Inovector\Mixpost\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
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
        Cache::put('mixpost-last-schedule-run', Carbon::now('utc'));

        Post::with('accounts')
            ->where('status', PostStatus::SCHEDULED->value)
            ->where('schedule_status', PostScheduleStatus::PENDING->value)
            ->where('scheduled_at', '<=', Carbon::now()->utc())
            ->each(function (Post $post) {
                (new PublishPost())($post);
            });

        return self::SUCCESS;
    }
}
