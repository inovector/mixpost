<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Inovector\Mixpost\MediaConversions\MediaImageResizeConversion;
use Inovector\Mixpost\MediaConversions\MediaVideoThumbConversion;
use Inovector\Mixpost\Models\Media;
use Inovector\Mixpost\Support\MediaUploader;

class MediaUploadFile extends FormRequest
{
    public function rules(): array
    {
        $mimes = collect(config('mixpost.mime_types'))->map(function ($mime) {
            return Str::after($mime, '/');
        })->implode(',');

        $maxFileSize = config('mixpost.max_file_size');

        return [
            'file' => ['required', 'file', "mimes:$mimes", "max:$maxFileSize"],
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
}
