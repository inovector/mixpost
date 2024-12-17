<?php

namespace Inovector\Mixpost\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Inovector\Mixpost\Support\MediaTemporaryDirectory;

class PruneTemporaryDirectory extends Command
{
    public $signature = 'mixpost:prune-temporary-directory {--hours=2}';

    public $description = 'Prune temporary directory';

    public function handle(): int
    {
        $temporaryDirectory = MediaTemporaryDirectory::getParentTemporaryDirectoryPath();

        if (!is_dir($temporaryDirectory)) {
            return self::SUCCESS;
        }

        $timeUnit = $this->option('hours') == 1 ? 'hour' : 'hours';
        $this->comment("Pruning files older than {$this->option('hours')} $timeUnit from the temporary directory...");

        $directories = scandir($temporaryDirectory);

        $totalDeletedFiles = 0;

        foreach ($directories as $directory) {
            if ($directory === '.' || $directory === '..') {
                continue;
            }

            $path = $temporaryDirectory . DIRECTORY_SEPARATOR . $directory;

            $diff = (int)abs(Carbon::now()->diffInHours(Carbon::createFromTimestamp(filemtime($path))));

            if ($diff >= $this->option('hours')) {
                exec("rm -rf $path");

                $totalDeletedFiles++;
            }
        }

        $this->info("Done! Total deleted files: $totalDeletedFiles");

        return self::SUCCESS;
    }
}
