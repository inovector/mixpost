<?php

namespace Inovector\Mixpost\Abstracts;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inovector\Mixpost\Contracts\MediaConversion as MediaConversionContract;
use Illuminate\Contracts\Filesystem\Filesystem;
use Inovector\Mixpost\Support\MediaConversionData;

abstract class MediaConversion implements MediaConversionContract
{
    protected string $name;
    protected string $fromDisk;
    protected string $toDisk = '';
    protected string $filepath;

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    public static function name(string $name): static
    {
        return new static($name);
    }

    public function fromDisk(string $name): static
    {
        $this->fromDisk = $name;

        return $this;
    }

    public function toDisk(string $name): static
    {
        $this->toDisk = $name;

        return $this;
    }

    public function filepath(string $path): static
    {
        $this->filepath = $path;

        return $this;
    }

    public function getEngineName(): string
    {
        return get_called_class();
    }

    public function getFileExtension(): string
    {
        return pathinfo($this->filesystem($this->getFromDisk())->path($this->getFilepath()), PATHINFO_EXTENSION);
    }

    public function getFileMimeType(): bool|string
    {
        return $this->filesystem($this->getFromDisk())->mimeType($this->getFilepath());
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function getFromDisk(): string
    {
        return $this->fromDisk;
    }

    public function getToDisk(): string
    {
        return $this->toDisk ?: $this->getFromDisk();
    }

    public function getFilePathWithSuffix($extension = null, $filepath = ''): string
    {
        $extension = $extension ?: $this->getFileExtension();

        return str_replace('.' . $this->getFileExtension(), '', $filepath ?: $this->getFilePath()) . "-$this->name.$extension";
    }

    public function isImage(): bool
    {
        return Str::before($this->getFileMimeType(), '/') === 'image';
    }

    public function isGifImage(): bool
    {
        return $this->isImage() && Str::after($this->getFileMimeType(), '/') === 'gif';
    }

    public function isVideo(): bool
    {
        return Str::before($this->getFileMimeType(), '/') === 'video';
    }

    public function perform(): ?MediaConversionData
    {
        if (!$this->canPerform()) {
            return null;
        }

        return $this->handle();
    }

    protected function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    protected function filesystem($disk = ''): Filesystem
    {
        return Storage::disk($disk ?: $this->getToDisk());
    }
}
