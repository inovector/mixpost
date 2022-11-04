<?php

namespace Inovector\Mixpost\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inovector\Mixpost\Models\Media;
use Inovector\Mixpost\Contracts\MediaConversion;

class MediaUploader
{
    protected UploadedFile $file;
    protected string $disk;
    protected string $path = '';
    protected array $conversions;

    public function __construct(UploadedFile $file)
    {
        $this->setFile($file);
        $this->disk(config('mixpost.disk'));
    }

    public static function fromFile(UploadedFile $file): static
    {
        return new static($file);
    }

    public function setFile(UploadedFile $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function disk(string $name): static
    {
        $this->disk = $name;

        return $this;
    }

    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function conversions(array $array): static
    {
        $this->conversions = $array;

        return $this;
    }

    public function upload(): array
    {
        $path = $this->filesystem()->putFile($this->path, $this->file, 'public');

        if (!$path) {
            throw new \Exception("The file was not uploaded. Check your $this->disk driver configuration.");
        }

        return [
            'name' => $this->file->getClientOriginalName(),
            'mime_type' => $this->file->getMimeType(),
            'size' => $this->file->getSize(),
            'size_total' => $this->file->getSize() + 0,
            'disk' => $this->disk,
            'path' => $path,
            'conversions' => $this->performConversions($path)
        ];
    }

    public function uploadAndInsert()
    {
        return Media::create($this->upload());
    }

    protected function performConversions(string $filepath): array
    {
        if (empty($this->conversions)) {
            return [];
        }

        return collect($this->conversions)->map(function ($conversion) use ($filepath) {
            if (!$conversion instanceof MediaConversion) {
                throw new \Exception('The conversion must be an instance of MediaConversion');
            }

            $perform = $conversion->filepath($filepath)->fromDisk($this->disk)->perform();

            if (!$perform) {
                return null;
            }

            return $perform->get();
        })->filter()->values()->toArray();
    }

    protected function filesystem(): Filesystem
    {
        return Storage::disk($this->disk);
    }
}
