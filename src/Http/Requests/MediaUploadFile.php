<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Inovector\Mixpost\MediaConversions\MediaImageResizeConversion;
use Inovector\Mixpost\MediaConversions\MediaVideoThumbConversion;
use Inovector\Mixpost\Models\Media;
use Inovector\Mixpost\Support\MediaUploader;
use Illuminate\Validation\Rules\File;

class MediaUploadFile extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', File::types($this->allowedTypes())->max($this->max())]
        ];
    }

    public function handle(): Media
    {
        $now = now()->format('m-Y');

        return MediaUploader::fromFile($this->file('file'))
            ->path("mixpost/$now")
            ->conversions([
                MediaImageResizeConversion::name('thumb')->width(430),
                MediaImageResizeConversion::name('large')->width(2000),
                MediaVideoThumbConversion::name('thumb')->atSecond(5)
            ])
            ->uploadAndInsert();
    }

    public function messages(): array
    {
        $fileType = $this->isImage() ? 'image' : 'video';
        $max = $this->max() / 1024;

        return [
            'file.max' => "The $fileType must not be greater than {$max}MB.",
        ];
    }

    private function isImage(): bool
    {
        return Str::before($this->file('file')->getMimeType(), '/') === 'image';
    }

    private function isVideo(): bool
    {
        return Str::before($this->file('file')->getMimeType(), '/') === 'video';
    }

    private function isGif(): bool
    {
        return Str::after($this->file('file')->getMimeType(), '/') === 'gif';
    }

    private function max()
    {
        $max = 0;

        if ($this->isImage()) {
            $max = config('mixpost.max_file_size.image');
        }

        if ($this->isGif()) {
            $max = config('mixpost.max_file_size.gif');
        }

        if ($this->isVideo()) {
            $max = config('mixpost.max_file_size.video');
        }

        return $max;
    }

    private function allowedTypes(): string
    {
        return collect(config('mixpost.mime_types'))->map(function ($mime) {
            return Str::after($mime, '/');
        })->implode(',');
    }
}
