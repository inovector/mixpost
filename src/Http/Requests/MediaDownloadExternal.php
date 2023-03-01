<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Inovector\Mixpost\MediaConversions\MediaImageResizeConversion;
use Inovector\Mixpost\Support\File;
use Illuminate\Support\Facades\Http;
use Inovector\Mixpost\Support\MediaUploader;

class MediaDownloadExternal extends FormRequest
{
    public function rules(): array
    {
        return [
            'items' => ['required', 'array']
        ];
    }

    public function handle(): Collection
    {
        return collect($this->input('items'))->map(function ($item) {
            $result = Http::get($item['url']);

            $now = now()->format('m-Y');

            $file = File::fromBase64(base64_encode($result->body()));

            return MediaUploader::fromFile($file)->path("mixpost/$now")->conversions([
                MediaImageResizeConversion::name('thumb')->width(430),
            ])->uploadAndInsert();
        });
    }
}
