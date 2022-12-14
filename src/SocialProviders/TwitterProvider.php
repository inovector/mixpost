<?php

namespace Inovector\Mixpost\SocialProviders;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inovector\Mixpost\Abstracts\SocialProvider;

class TwitterProvider extends SocialProvider
{
    const VERSION_API_ONE = '1.1';
    const API_VERSION_TWO = '2';

    public TwitterOAuth $connection;

    // Overwrite __construct to use Twitter SDK
    public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl)
    {
        $this->connection = new TwitterOAuth($clientId, $clientSecret);
        $this->connection->setApiVersion(self::API_VERSION_TWO);
        $this->connection->setTimeouts(10, 60);

        parent::__construct($request, $clientId, $clientSecret, $redirectUrl);
    }

    public function getAuthUrl(): string
    {
        $response = $this->connection->oauth('oauth/request_token', ['x_auth_access_type' => 'write', 'redirect_uri' => $this->redirectUrl]);

        return $this->connection->url('oauth/authorize', ['oauth_token' => $response['oauth_token'], 'oauth_token_secret' => $response['oauth_token_secret']]);
    }

    public function requestAccessToken(array $params = []): array
    {
        $response = $this->connection->oauth('oauth/access_token', ['oauth_token' => $this->request->get('oauth_token'), 'oauth_verifier' => $this->request->get('oauth_verifier')]);

        return [
            'oauth_token' => $response['oauth_token'],
            'oauth_token_secret' => $response['oauth_token_secret']
        ];
    }

    // Overwrite setAccessToken to use Twitter SDK
    public function setAccessToken(array $token = []): void
    {
        $this->connection->setOauthToken($token['oauth_token'], $token['oauth_token_secret']);
    }

    // Overwrite useAccessToken to use Twitter SDK
    public function useAccessToken(array $token = []): static
    {
        $this->connection->setOauthToken($token['oauth_token'], $token['oauth_token_secret']);

        return $this;
    }

    public function getAccount(array $params = []): array
    {
        $response = $this->connection->get('users/me', ['user.fields' => 'profile_image_url,created_at']);

        return [
            'id' => $response->data->id,
            'name' => $response->data->name,
            'username' => $response->data->username,
            'image' => str_replace('normal', '400x400', $response->data->profile_image_url)
        ];
    }

    public function publishPost(string $text, array $media = [], array $params = []): array
    {
        // Upload media
        $mediaResult = $this->uploadMedia($media);

        if (!empty($mediaResult['errors'])) {
            return [
                'errors' => $mediaResult['errors']
            ];
        }

        // Publish post with media
        $this->connection->setApiVersion(self::API_VERSION_TWO);

        $postParameters = ['text' => $text];

        if (!empty($mediaResult['ids'])) {
            $postParameters['media'] = [
                'media_ids' => $mediaResult['ids']
            ];
        }

        $postResult = $this->connection->post('tweets', $postParameters, true);

        $errors = Arr::map($postResult->errors ?? [], function ($error) {
            return $error->message;
        });

        if (isset($postResult->status)) {
            $errors[] = $postResult->detail;
        }

        if (!empty($errors)) {
            return [
                'errors' => $errors
            ];
        }

        return [
            'id' => $postResult->data->id
        ];
    }

    public function uploadMedia(array $media): array
    {
        $this->connection->setApiVersion(self::VERSION_API_ONE);

        $ids = [];
        $errors = [];

        foreach ($media as $item) {
            $isGif = Str::after($item['mime_type'], '/') === 'gif';
            $chunkUpload = !$item['is_image'] || $isGif;

            if (!$chunkUpload) {
                $result = $this->connection->upload('media/upload', [
                    'media' => $item['path'],
                    'media_type' => $item['mime_type'],
                    'media_category' => 'tweet_image',
                    'total_bytes' => $item['size'],
                ]);
            }

            if ($chunkUpload) {
                $result = $this->connection->upload('media/upload', [
                    'media' => $item['path'],
                    'media_type' => $item['mime_type'],
                    'media_category' => $isGif ? 'tweet_gif' : 'tweet_video',
                    'total_bytes' => $item['size'],
                ], true);
            }

            if (!$result) {
                $errors[] = $result;
                continue;
            }

            // Check status of uploaded media
            if (isset($result->processing_info)) {
                $state = $result->processing_info->state;
                $sleepSeconds = $result->processing_info->check_after_secs;

                do {
                    sleep($sleepSeconds);

                    $mediaStatus = $this->connection->mediaStatus($result->media_id);

                    $state = $mediaStatus->processing_info->state;
                    $sleepSeconds = $mediaStatus->processing_info->check_after_secs ?? 1;

                } while (in_array($state, ['pending', 'in_progress']));

                if ($state === 'failed') {
                    $errors[] = "Failed to upload {$item['name']} file.";
                    continue;
                }
            }

            $ids[] = $result->media_id_string;
        }

        return [
            'ids' => $ids,
            'errors' => $errors
        ];
    }

    public function uploadVideo()
    {

    }

    public function deletePost()
    {
        // TODO: Implement deletePost() method.
    }

    public function getMetrics()
    {
        // TODO: Implement metrics() method.
    }
}
