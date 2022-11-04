<?php

namespace Inovector\Mixpost\Support;

use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory as BaseTemporaryDirectory;

class MediaTemporaryDirectory
{
    public static function create(): BaseTemporaryDirectory
    {
        return new BaseTemporaryDirectory(static::getTemporaryDirectoryPath());
    }

    protected static function getTemporaryDirectoryPath(): string
    {
        $path = config('mixpost.temporary_directory_path') ?? storage_path('mixpost-media/temp');

        return $path . DIRECTORY_SEPARATOR . Str::random(32);
    }
}
