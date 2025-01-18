<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Inovector\Mixpost\Models\Media;
use Inovector\Mixpost\Models\PostVersion;

class DeleteMedia extends FormRequest
{
    public function rules(): array
    {
        return [
            'items' => ['required', 'array']
        ];
    }

    public function handle(): void
    {
        foreach ($this->input('items') as $id) {
            $media = Media::find($id);

            if (!$media) {
                continue;
            }

            $postVersions = PostVersion::hasMedia($media)->get();
            foreach ($postVersions as $postVersion) {
                $postVersion->removeMedia($media);
            }

            $media->deleteFiles();
            $media->delete();
        }
    }
}
