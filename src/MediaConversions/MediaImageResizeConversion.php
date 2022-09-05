<?php

namespace Inovector\Mixpost\MediaConversions;

use Inovector\Mixpost\Abstracts\MediaConversion;
use Inovector\Mixpost\Support\MediaConversionData;
use Intervention\Image\Facades\Image;

class MediaImageResizeConversion extends MediaConversion
{
    protected float|null $width;
    protected float|null $height = null;

    public function getEngineName(): string
    {
        return 'ImageResize';
    }

    public function canPerform(): bool
    {
        return $this->isImage();
    }

    public function getPath(): string
    {
        return $this->getFilePathWithSuffix();
    }

    public function width(float|null $value = null): static
    {
        $this->width = $value;

        return $this;
    }

    public function height(float|null $value = null): static
    {
        $this->height = $value;

        return $this;
    }

    public function handle(): MediaConversionData|null
    {
        $image = Image::make($this->filesystem($this->getFromDisk())->url($this->getFilepath()));

        $convert = $image->resize($this->width, $this->height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode();

        $this->filesystem()->put($this->getPath(), $convert->getEncoded(), 'public');

        return MediaConversionData::conversion($this);
    }
}
