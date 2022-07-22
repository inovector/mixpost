<?php

namespace Lao9s\Mixpost\Commands;

use Illuminate\Console\Command;

class MixpostCommand extends Command
{
    public $signature = 'mixpost';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
