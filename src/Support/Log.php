<?php

namespace Inovector\Mixpost\Support;

use Illuminate\Support\Facades\Log as LogFacade;

class Log
{
    public static function info(string $message, array $context = []): void
    {
        LogFacade::stack(self::stack())->info($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        LogFacade::stack(self::stack())->error($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        LogFacade::stack(self::stack())->warning($message, $context);
    }

    protected static function stack(): array
    {
        if ($channel = config('mixpost.log_channel')) {
            return [$channel];
        }

        return [config('app.log_channel')];
    }
}
