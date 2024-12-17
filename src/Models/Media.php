<?php

namespace Inovector\Mixpost\Models;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inovector\Mixpost\Concerns\Model\HasUuid;
use Inovector\Mixpost\Support\MediaFilesystem;
use Inovector\Mixpost\Support\MediaTemporaryDirectory;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Exception;

class Media extends Model
{
    use HasFactory;
    use HasUuid;

    public $table = 'mixpost_media';

    protected $fillable = [
        'name',
        'mime_type',
        'disk',
        'path',
        'size',
        'size_total',
        'conversions'
    ];

    protected $casts = [
        'id' => 'string',
        'conversions' => 'array'
    ];

    public function getFullPath(): string
    {
        if ($this->disk === 'external_media') {
            return $this->path;
        }

        return $this->filesystem()->path($this->path);
    }

    public function getUrl(): string
    {
        if ($this->disk === 'external_media') {
            return $this->path;
        }

        return $this->filesystem()->url($this->path);
    }

    public function contents(?string $conversionName = null): ?string
    {
        if ($this->disk === 'external_media') {
            return null;
        }

        $disk = $this->disk;
        $path = $this->path;

        if ($conversionName) {
            if ($conversion = $this->getConversion($conversionName)) {
                $disk = $conversion['disk'];
                $path = $conversion['path'];
            }
        }

        return $this->filesystem($disk)->get($path);
    }

    /**
     * When you call this function, you must delete the temporary file
     * $readStream['temporaryDirectory']?->delete();
     */
    public function readStream(?string $conversionName = null): ?array
    {
        if ($this->disk === 'external_media') {
            return null;
        }

        $disk = $this->disk;
        $path = $this->path;

        if ($conversionName) {
            if ($conversion = $this->getConversion($conversionName)) {
                $disk = $conversion['disk'];
                $path = $conversion['path'];
            }
        }

        // Download from external adapter (s3...etc.) and read the stream
        if (!$this->isLocalAdapter()) {
            $temporaryDirectory = MediaTemporaryDirectory::create();
            $tempFilePath = $temporaryDirectory->path($path);
            MediaFilesystem::copyFromDisk($path, $disk, $tempFilePath);

            return [
                'stream' => fopen($tempFilePath, 'r'),
                'temporaryDirectory' => $temporaryDirectory,
            ];
        }

        return [
            'stream' => $this->filesystem($disk)->readStream($path),
            'temporaryDirectory' => null,
        ];
    }

    public function downloadToTemp(): array
    {
        if ($this->isLocalAdapter()) {
            throw new Exception('This function only works with remote adapters');
        }

        $disk = $this->disk;
        $path = $this->path;

        $temporaryDirectory = MediaTemporaryDirectory::create();
        $tempFilePath = $temporaryDirectory->path($path);
        MediaFilesystem::copyFromDisk($path, $disk, $tempFilePath);

        return [
            'temporaryDirectory' => $temporaryDirectory,
            'fullPath' => $tempFilePath,
        ];
    }

    public function getThumbFullPath(): ?string
    {
        return $this->getConversionFullPath('thumb');
    }

    public function getThumbUrl(): ?string
    {
        return $this->getConversionUrl('thumb');
    }

    public function getConversion(string $name): ?array
    {
        return collect($this->conversions)->where('name', $name)->first();
    }

    public function getConversionUrl(string $name): ?string
    {
        if ($conversion = $this->getConversion($name)) {
            if ($this->disk === 'external_media') {
                return $conversion['path'];
            }

            return $this->filesystem($conversion['disk'])->url($conversion['path']);
        }

        return null;
    }

    public function getConversionFullPath(string $name): ?string
    {
        if ($conversion = $this->getConversion($name)) {
            if ($this->disk === 'external_media') {
                return $conversion['path'];
            }

            return $this->filesystem($conversion['disk'])->path($conversion['path']);
        }

        return null;
    }

    public function getAdapter(): FilesystemAdapter
    {
        return $this->filesystem($this->disk)->getAdapter();
    }

    public function isLocalAdapter(): bool
    {
        $adapter = $this->getAdapter();

        return $adapter instanceof LocalFilesystemAdapter;
    }

    public function deleteFiles(): void
    {
        $this->filesystem()->delete($this->path);

        foreach ($this->conversions as $conversion) {
            $this->filesystem($conversion['disk'])->delete($conversion['path']);
        }
    }

    public function filesystem(string $disk = ''): Filesystem
    {
        return Storage::disk($disk ?: $this->disk);
    }

    public function isImage(): bool
    {
        return Str::before($this->mime_type, '/') === 'image';
    }

    public function isImageGif(): bool
    {
        return $this->isImage() && Str::after($this->mime_type, '/') === 'gif';
    }

    public function isVideo(): bool
    {
        return Str::before($this->mime_type, '/') === 'video';
    }

    public function type(): string
    {
        if ($this->isVideo()) {
            return 'video';
        }

        if ($this->isImageGif()) {
            return 'gif';
        }

        return 'image';
    }
}
