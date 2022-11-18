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

        $provider = SocialProviderManager::connect($account->provider)->useAccessToken($account->access_token);

        try {
            $response = $provider->publishPost($body, $media, params: ['provider_id' => $account->provider_id]);

            if (isset($response['errors'])) {
                $this->insertErrors($post, $account, $response['errors']);
            }

            if (!isset($response['errors'])) {
                $this->insertProviderPostId($post, $account, $response['id']);
            }
        } catch (Exception $exception) {
            Log::error("Publishing error: {$exception->getMessage()}",
                array_merge([
                    'post_id' => $post->id,
                    'account_id' => $account->id,
                    'account_name' => $account->name,
                    'account_provider' => $account->provider
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
            'provider_post_id' => $id
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
        return str_replace(["<div>", "</div>"], ["", "\n"], $text);
    }

    private function collectMedia(array $media): array
    {
        return Media::whereIn('id', $media)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'path' => $item->getLargeFullPath(),
                'name' => $item->name,
                'mime_type' => $item->mime_type,
                'size' => $item->size
            ];
        })->toArray();
    }
}
