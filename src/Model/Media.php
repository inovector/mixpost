<?php

namespace Inovector\Mixpost\Model;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function getUrl(): string
    {
        return $this->filesystem()->url($this->path);
    }

    public function getThumbUrl(): ?string
    {
        return $this->getConversionUrl('thumb');
    }

    public function getConversionUrl(string $name): ?string
    {
        $conversion = collect($this->conversions)->where('name', $name)->first();

        if (!$conversion) {
            return null;
        }

        return $this->filesystem($conversion['disk'])->url($conversion['path']);
    }

    public function filesystem(string $disk = ''): Filesystem
    {
        return Storage::disk($disk ?: $this->disk);
    }
}
