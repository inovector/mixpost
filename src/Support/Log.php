<?php

namespace Inovector\Mixpost\Support;

use Illuminate\Support\Facades\Log as LogFacade;

class Log
{
    static public function info(string $message, array $context = []): void
    {
        LogFacade::stack(self::stack())->info($message, $context);
    }

    static public function error(string $message, array $context = []): void
    {
        LogFacade::stack(self::stack())->error($message, $context);
    }

    static public function warning(string $message, array $context = []): void
    {
        LogFacade::stack(self::stack())->warning($message, $context);
    }

    static protected function stack(): array
    {
        if ($channel = config('mixpost.log_channel')) {
            return [$channel];
        }

        return [config('app.log_channel')];
    }
}
