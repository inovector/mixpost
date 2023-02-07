<?php

namespace Inovector\Mixpost\SocialProviders;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inovector\Mixpost\Abstracts\SocialProvider;

class MastodonProvider extends SocialProvider
{
    public array $callbackResponseKeys = ['code'];

    protected string $apiVersion = 'v1';
    protected string $serverUrl;

    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $options = [])
    {
        $this->serverUrl = "https://{$options['server']}";

        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $options);
    }

    public function getAuthUrl(): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => 'read write',
            'response_type' => 'code',
            'state' => null
        ];

        return $this->buildUrlFromBase("$this->serverUrl/oauth/authorize", $params);
    }

    public function requestAccessToken(array $params = []): array
    {
        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUrl,
            'grant_type' => 'authorization_code',
            'code' => $params['code'],
            'scope' => 'read write'
        ];

        $response = Http::post("$this->serverUrl/oauth/token", $params)->json();

        return [
            'access_token' => $response['access_token']
        ];
    }

    public function getAccount(array $params = []): array
    {
        $response = Http::withToken($this->getAccessToken()['access_token'])
            ->get("$this->serverUrl/api/$this->apiVersion/accounts/verify_credentials", $params)
            ->json();

        return [
            'id' => $response['id'],
            'name' => $response['display_name'],
            'username' => $response['username'],
            'image' => $response['avatar'],
            'data' => [
                'server' => $this->options['server']
            ]
        ];
    }

    public function publishPost(string $text, array $media = [], array $params = []): array
    {
        // Upload media

        return [];
        // Post
    }

    public function uploadMedia(array $media): array
    {
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

    public function deletePost()
    {
        // TODO: Implement deletePost() method.
    }

    public function getMetrics()
    {
        // TODO: Implement metrics() method.
    }
}
