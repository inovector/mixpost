<?php

namespace Inovector\Mixpost\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Inovector\Mixpost\Models\FacebookInsight;
use Inovector\Mixpost\Models\ImportedPost;

class DeleteOldData extends Command
{
    public $signature = 'mixpost:delete-old-data';

    public $description = "Delete old data from social service providers";

    public function handle(): int
    {
        ImportedPost::where('created_at', '<', Carbon::now()->subDays(95)->toDateString())->delete();
        FacebookInsight::where('date', '<', Carbon::now()->subDays(95)->toDateString())->delete();

        return self::SUCCESS;
    }
}
