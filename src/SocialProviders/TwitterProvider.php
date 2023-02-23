<?php

namespace Inovector\Mixpost\SocialProviders;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inovector\Mixpost\Abstracts\SocialProvider;

class TwitterProvider extends SocialProvider
{
    protected string $apiVersion = '2';

    public TwitterOAuth $connection;

    // Overwrite __construct to use Twitter SDK
    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $values = [])
    {
        $this->connection = new TwitterOAuth($clientId, $clientSecret);
        $this->connection->setApiVersion($this->apiVersion);
        $this->connection->setTimeouts(10, 60);

        parent::__construct($request, $clientId, $clientSecret, $redirectUrl);
    }

    public function getAuthUrl(): string
    {
        $result = $this->connection->oauth('oauth/request_token', ['x_auth_access_type' => 'write', 'redirect_uri' => $this->redirectUrl]);

        return $this->connection->url('oauth/authorize', ['oauth_token' => $result['oauth_token'], 'oauth_token_secret' => $result['oauth_token_secret']]);
    }

    public function requestAccessToken(array $params = []): array
    {
        $result = $this->connection->oauth('oauth/access_token', ['oauth_token' => $this->request->get('oauth_token'), 'oauth_verifier' => $this->request->get('oauth_verifier')]);

        return [
            'oauth_token' => $result['oauth_token'],
            'oauth_token_secret' => $result['oauth_token_secret']
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

    public function getAccount(): array
    {
        $result = $this->connection->get('users/me', ['user.fields' => 'profile_image_url,created_at']);

        if (isset($result->status)) {
            return $this->buildErrorResponse($result->status, $result->detail);
        }

        return [
            'id' => $result->data->id,
            'name' => $result->data->name,
            'username' => $result->data->username,
            'image' => str_replace('normal', '400x400', $result->data->profile_image_url)
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

        $postParameters = ['status' => $text];

        if (!empty($mediaResult['ids'])) {
            $postParameters['media_ids'] = implode(',', $mediaResult['ids']);
        }

        $this->connection->setApiVersion('1.1');

        $postResult = $this->connection->post('statuses/update', $postParameters);

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
            'id' => $postResult->id
        ];
    }

    public function uploadMedia(array $media): array
    {
        $this->connection->setApiVersion('1.1');

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

    public function getAccountMetrics(): array
    {
        $result = $this->connection->get('users/me', ['user.fields' => 'public_metrics']);

        if (isset($result->status)) {
            return $this->buildErrorResponse($result->status, $result->detail);
        }

        return [
            'followers_count' => $result->data->public_metrics->followers_count,
            'following_count' => $result->data->public_metrics->following_count,
            'tweet_count' => $result->data->public_metrics->tweet_count,
            'listed_count' => $result->data->public_metrics->listed_count,
        ];
    }

    public function getUserTweetTimeline(string $userId, string $paginationToken = '')
    {
        $params = [
            'tweet.fields' => 'public_metrics,created_at,in_reply_to_user_id',
            'start_time' => Carbon::now()->subMonths(3)->startOfDay()->toRfc3339String(),
            'exclude' => 'retweets,replies',
            'max_results' => 100
        ];

        if ($paginationToken) {
            $params['pagination_token'] = $paginationToken;
        }

        $result = $this->connection->get("users/$userId/tweets", $params);

        if (isset($result->status)) {
            return $this->buildErrorResponse($result->status, $result->detail);
        }

        return [
            'data' => $result->data ?? [],
            'meta' => $result->meta ?? null,
        ];
    }

    public function deletePost()
    {
        // TODO: Implement deletePost() method.
    }
}
