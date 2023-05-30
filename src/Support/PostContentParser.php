<?php

namespace Inovector\Mixpost\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Media;
use Inovector\Mixpost\Models\Post;

class PostContentParser
{
    public function __construct(
        private readonly Account $account,
        private readonly Post    $post
    )
    {
    }

    public function getVersionContent(): array
    {
        $accountVersion = $this->post->versions->where('account_id', $this->account->id)->first();

        if (empty($accountVersion)) {
            return $this->post->versions->where('is_original', true)->first()->content ?: [];
        }

        return $accountVersion->content;
    }

    public function getVersionOptions(): array
    {
        $accountVersion = $this->post->versions->where('account_id', $this->account->id)->first();

        if (empty($accountVersion)) {
            $original = $this->post->versions->where('is_original', true)->first();

            return Arr::get($original->options ?? [], $this->account->provider, []);
        }

        return Arr::get($accountVersion->options ?? [], $this->account->provider, []);
    }

    public function formatBody(?string $text): string
    {
        if (!$text) {
            return '';
        }

        $replaceDiv = str_replace(["<div>", "</div>"], ["", "\n"], $text);

        $decode = html_entity_decode($replaceDiv);

        return strip_tags($decode);
    }

    public function formatMedia(array $ids): Collection
    {
        $media = Media::whereIn('id', $ids)->get();

        return collect($ids)->map(function ($id) use ($media) {
            return $media->where('id', $id)->first();
        })->filter();
    }
}
