<?php

namespace Inovector\Mixpost\MediaConversions;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\File;
use Inovector\Mixpost\Abstracts\MediaConversion;
use Inovector\Mixpost\Support\MediaConversionData;
use Inovector\Mixpost\Support\MediaFilesystem;
use Inovector\Mixpost\Support\MediaTemporaryDirectory;

class MediaVideoThumbConversion extends MediaConversion
{
    protected float $atSecond = 0;

    public function getEngineName(): string
    {
        return 'VideoThumb';
    }

    public function canPerform(): bool
    {
        return $this->isVideo();
    }

    public function getPath(): string
    {
        return $this->getFilePathWithSuffix('jpg');
    }

    public function atSecond(float $value = 0): static
    {
        $this->atSecond = $value;

        return $this;
    }

    public function handle(): MediaConversionData|null
    {
        // Create & copy to temporary directory
        $temporaryDirectory = MediaTemporaryDirectory::create();

        $file = $temporaryDirectory->path($this->getFilepath());
        $thumbFilepath = $this->getFilePathWithSuffix('jpg', $file);

        MediaFilesystem::copyFromDisk($this->getFilepath(), $this->getFromDisk(), $file);

        // Convert
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('mixpost.ffmpeg_path'),
            'ffprobe.binaries' => config('mixpost.ffprobe_path'),
        ]);

        $video = $ffmpeg->open($file);
        $duration = $ffmpeg->getFFProbe()->format($file)->get('duration');

        // Ensure $seconds is within valid bounds
        $seconds = ($duration > 0 && $this->atSecond > 0) ? min($this->atSecond, floor($duration)) : 0;

        $frame = $video->frame(TimeCode::fromSeconds($seconds));
        $frame->save($thumbFilepath);

        // Sometimes the frame is not saved, so we save it again with the first frame
        // This is a workaround for the issue
        if ($this->atSecond !== 0 && !File::exists($thumbFilepath)) {
            $frame = $video->frame(TimeCode::fromSeconds(0));
            $frame->save($thumbFilepath);
        }

        // Copy
        MediaFilesystem::copyToDisk($this->getToDisk(), $this->getPath(), $thumbFilepath);

        // Delete temporary directory
        $temporaryDirectory->delete();

        return MediaConversionData::conversion($this);
    }
}
