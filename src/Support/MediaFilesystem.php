<?php

namespace Inovector\Mixpost\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaFilesystem
{
    public static function copyFromDisk(string $sourceFilepath, string $sourceDisk, string $targetFile): bool|int
    {
        return File::put($targetFile, self::getStream($sourceFilepath, $sourceDisk));
    }

    public static function copyToDisk(string $targetDisk, string $targetFile, string $sourceFile): bool
    {
        return Storage::disk($targetDisk)->put($targetFile, File::get($sourceFile), 'public');
    }

    protected static function getStream(string $filepath, string $disk)
    {
        return Storage::disk($disk)->readStream($filepath);
    }
}
