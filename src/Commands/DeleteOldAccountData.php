<?php

namespace Inovector\Mixpost\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Inovector\Mixpost\Models\ImportedPost;

class DeleteOldAccountData extends Command
{
    public $signature = 'mixpost:delete-old-account-data';

    public $description = "Delete old data from social service providers";

    public function handle(): int
    {
        $delete = ImportedPost::where('created_at', '<', Carbon::now()->subDays(90)->toDateString())->delete();

        if (!$delete) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
