<?php

namespace Inovector\Mixpost\Models;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media extends Model
{
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
        'conversions' => 'array'
    ];

    public function getFullPath(): string
    {
        return $this->filesystem()->path($this->path);
    }

    public function getUrl(): string
    {
        return $this->filesystem()->url($this->path);
    }

    public function getThumbUrl(): ?string
    {
        return $this->getConversionUrl('thumb');
    }

    public function getLargeFullPath(): ?string
    {
        return $this->getConversionFullPath('large');
    }

    public function getConversion(string $name): ?array
    {
        return collect($this->conversions)->where('name', $name)->first();
    }

    public function getConversionUrl(string $name): ?string
    {
        if ($conversion = $this->getConversion($name)) {
            return $this->filesystem($conversion['disk'])->url($conversion['path']);
        }

        return null;
    }

    public function getConversionFullPath(string $name): ?string
    {
        if ($conversion = $this->getConversion($name)) {
            return $this->filesystem($conversion['disk'])->path($conversion['path']);
        }

        return null;
    }

    public function filesystem(string $disk = ''): Filesystem
    {
        return Storage::disk($disk ?: $this->disk);
    }

    public function isImage(): bool
    {
        return Str::before('/', $this->mime_type) === 'image';
    }

    public function isVideo(): bool
    {
        return Str::before('/', $this->mime_type) === 'video';
    }
}
