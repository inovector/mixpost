<?php

namespace Inovector\Mixpost\Actions;

use Inovector\Mixpost\Concerns\UsesSocialProviderManager;
use Inovector\Mixpost\Enums\SocialProviderResponseStatus;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Post;
use Inovector\Mixpost\Support\PostContentParser;
use Inovector\Mixpost\Support\SocialProviderResponse;

class AccountPublishPost
{
    use UsesSocialProviderManager;

    public function __invoke(Account $account, Post $post): SocialProviderResponse
    {
        $parser = new PostContentParser($account, $post);

        $content = $parser->getVersionContent();

        if (empty($content)) {
            $errors = ['This account version has no content.'];

            $this->insertErrors($post, $account, $errors);

            return new SocialProviderResponse(SocialProviderResponseStatus::ERROR, $errors);
        }

        $response = $this->connectProvider($account)->publishPost(
            text: $parser->formatBody($content[0]['body']),
            media: $parser->formatMedia($content[0]['media']),
            params: $parser->getVersionOptions()
        );

        if ($response->hasError()) {
            // TODO: Create a column for system error in `mixpost_post_accounts`
            $this->insertErrors($post, $account, $response->context());

            return $response;
        }

        $this->insertProviderPostData($post, $account, $response);

        return $response;
    }

    private function insertErrors(Post $post, Account $account, $errors): void
    {
        $post->accounts()->updateExistingPivot($account->id, [
            'errors' => json_encode($errors)
        ]);
    }

    private function insertProviderPostData(Post $post, Account $account, SocialProviderResponse $response): void
    {
        $post->accounts()->updateExistingPivot($account->id, [
            'provider_post_id' => $response->id,
// TODO: add `data` column to `mixpost_post_accounts` table
//            'data' => $response->data ? json_encode($response->data) : null,
            'errors' => null,
        ]);
    }
}
