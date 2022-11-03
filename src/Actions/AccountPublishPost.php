<?php

namespace Inovector\Mixpost\Actions;

use Illuminate\Database\Eloquent\Collection;
use Inovector\Mixpost\Exceptions\MissingAccountContent;
use Inovector\Mixpost\Facades\SocialProviderManager;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Media;
use Inovector\Mixpost\Models\Post;

class AccountPublishPost
{
    public function __invoke(Account $account, Post $post): bool|array
    {
        $content = $this->getContentVersion($account, $post->versions);

        if (empty($content)) {
            $errors = "This account doesn't have content!";

            $this->setError($post, $account, $errors);

            throw new MissingAccountContent($errors);
        }

        $body = $this->cleanBody($content[0]['body']);
        $media = $this->collectMedia($content[0]['media']);

        $provider = SocialProviderManager::connect($account->provider);
        $provider->setCredentials($account->credentials);

        $response = $provider->publishPost($body, $media);

        if (!empty($response['errors'])) {
            $this->setError($post, $account, $response['errors']);

            return [
                'errors' => $response['errors']
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
