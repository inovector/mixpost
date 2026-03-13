<?php

namespace Inovector\Mixpost\MediaConversions;

use Inovector\Mixpost\Abstracts\MediaConversion;
use Inovector\Mixpost\Support\MediaConversionData;
use Intervention\Image\Facades\Image;

class MediaImageResizeConversion extends MediaConversion
{
    protected ?float $width;

    protected ?float $height = null;

    public function getEngineName(): string
    {
        return 'ImageResize';
    }

    public function canPerform(): bool
    {
        return $this->isImage() && ! $this->isGifImage();
    }

    public function getPath(): string
    {
        return $this->getFilePathWithSuffix();
    }

    public function width(?float $value = null): static
    {
        $this->width = $value;

        return $this;
    }

    public function height(?float $value = null): static
    {
        $this->height = $value;

        return $this;
    }

    public function handle(): ?MediaConversionData
    {
        // TODO: Check if works with S3 driver
        $content = $this->filesystem($this->getFromDisk())->get($this->getFilepath());

        $image = Image::make($content);

        $convert = $image->resize($this->width, $this->height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode();

        $this->filesystem()->put($this->getPath(), $convert->getEncoded(), 'public');

        return MediaConversionData::conversion($this);
    }
}
