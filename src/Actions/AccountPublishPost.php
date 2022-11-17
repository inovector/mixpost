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
    public function __invoke(Account $account, Post $post): bool|array
    {
        $content = $this->getContentVersion($account, $post->versions);

        if (empty($content)) {
            $error = "This account doesn't have content!";

            $this->setError($post, $account, [$error]);
        }

        $body = $this->cleanBody($content[0]['body']);
        $media = $this->collectMedia($content[0]['media']);

        $provider = SocialProviderManager::connect($account->provider);
        $provider->setAccessToken($account->access_token);

        try {
            $response = $provider->publishPost($body, $media);
            $errors = $response['errors'];

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
        }

        if (!empty($errors)) {
            $this->setError($post, $account, $errors);

            return [
                'errors' => $errors
            ];
        }

        return true;
    }

    private function setError(Post $post, Account $account, $errors): void
    {
        $post->accounts()->updateExistingPivot($account->id, [
            'errors' => json_encode($errors)
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

    private function collectMedia(array $media)
    {
        return Media::whereIn('id', $media)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'path' => $item->getLargeFullPath(),
                'name' => $item->name,
                'mime_type' => $item->mime_type,
                'size' => $item->size
            ];
        });
    }

    private function cleanBody(string $text): string
    {
        return str_replace(["<div>", "</div>"], ["", "\n"], $text);
    }
}
