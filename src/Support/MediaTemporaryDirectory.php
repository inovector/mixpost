<?php

namespace Inovector\Mixpost\Support;

use Illuminate\Support\Str;
use Inovector\Mixpost\Util;
use Spatie\TemporaryDirectory\TemporaryDirectory as BaseTemporaryDirectory;

class MediaTemporaryDirectory
{
    public static function create(): BaseTemporaryDirectory
    {
        return new BaseTemporaryDirectory(static::getTemporaryDirectoryPath());
    }

    public static function getParentTemporaryDirectoryPath()
    {
        return Util::config('temporary_directory_path') ?? storage_path('mixpost-media/temp');
    }

    public static function getTemporaryDirectoryPath(): string
    {
        return self::getParentTemporaryDirectoryPath() . DIRECTORY_SEPARATOR . Str::random(32);
    }
}
