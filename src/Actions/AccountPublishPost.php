<?php

namespace Inovector\Mixpost\Actions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Inovector\Mixpost\Facades\SocialProviderManager;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Media;
use Inovector\Mixpost\Models\Post;
use Exception;

class AccountPublishPost
{
    public function __invoke(Account $account, Post $post): void
    {
        $content = $this->getContentVersion($account, $post->versions);

        if (empty($content)) {
            $errors = ["This account version doesn't have content!"];

            $this->insertErrors($post, $account, $errors);
        }

        $body = $this->cleanBody($content[0]['body']);
        $media = $this->collectMedia($content[0]['media']);

        $provider = SocialProviderManager::connect($account->provider, $account->values())->useAccessToken($account->access_token->toArray());

        try {
            $response = $provider->publishPost($body, $media);

            if (isset($response['errors'])) {
                $this->insertErrors($post, $account, $response['errors']);
            }

            if (isset($response['id'])) {
                $this->insertProviderPostId($post, $account, $response['id']);
            }
        } catch (Exception $exception) {
            Log::error("Publishing error: {$exception->getMessage()}",
                array_merge([
                    'post_id' => $post->id,
                    'account_id' => $account->id,
                    'account_name' => $account->name,
                    'account_provider' => $account->provider,
                    'trace' => $exception->getTrace(),
                ])
            );

            $errors = ['Unexpected internal error.'];

            $this->insertErrors($post, $account, $errors);
        }
    }

    private function insertErrors(Post $post, Account $account, $errors): void
    {
        $post->accounts()->updateExistingPivot($account->id, [
            'errors' => json_encode($errors)
        ]);
    }

    private function insertProviderPostId(Post $post, Account $account, string $id): void
    {
        $post->accounts()->updateExistingPivot($account->id, [
            'provider_post_id' => $id,
            'errors' => null,
        ]);
    }

    private function getContentVersion(Account $account, Collection $versions)
    {
        $accountVersion = $versions->where('account_id', $account->id)->first();

        if (empty($accountVersion)) {
            return $versions->where('is_original', true)->first()->content ?: null;
        }

        return $accountVersion->content;
    }

    private function cleanBody(string $text): string
    {
        $replaceDiv = str_replace(["<div>", "</div>"], ["", "\n"], $text);

        return strip_tags($replaceDiv);
    }

    private function collectMedia(array $ids): array
    {
        $items = [];

        $media = Media::whereIn('id', $ids)->get()->keyBy('id');

        foreach ($ids as $id) {
            $item = $media[$id] ?? null;

            if (!$item) {
                continue;
            }

            $items[] = [
                'id' => $item->id,
                'path' => $item->getFullPath(),
                'thumb_path' => $item->getThumbFullPath(),
                'name' => $item->name,
                'mime_type' => $item->mime_type,
                'is_image' => $item->isImage(),
                'size' => $item->size
            ];
        }

        return $items;
    }
}
