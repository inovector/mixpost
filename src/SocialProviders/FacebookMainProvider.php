<?php

namespace Inovector\Mixpost\SocialProviders;

use Illuminate\Support\Facades\Http;
use Inovector\Mixpost\Abstracts\SocialProvider;

class FacebookMainProvider extends SocialProvider
{
    public array $callbackResponseKeys = ['code'];

    protected string $apiVersion = 'v15.0';
    protected string $apiUrl = 'https://graph.facebook.com';

    public function getAuthUrl(): string
    {
        return '';
    }

    public function requestAccessToken(array $params = []): array
    {
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $params['code'],
            'redirect_uri' => $this->redirectUrl,
        ];

        $response = Http::post("$this->apiUrl/$this->apiVersion/oauth/access_token", $params);

        $data = json_decode($response->body(), true);

        if (isset($data['error'])) {
            return [
                'error' => $data['error']['message']
            ];
        }

        return $this->requestLongLivedAccessToken($data['access_token']);
    }

    public function requestLongLivedAccessToken(string $shortLivedAccessToken = '')
    {
        $params = [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'fb_exchange_token' => $shortLivedAccessToken ?: $this->getAccessToken()['access_token']
        ];

        $response = Http::post("$this->apiUrl/$this->apiVersion/oauth/access_token", $params);

        return json_decode($response->body(), true);
    }

    public function getUserAccount(): array
    {
        $response = Http::get("$this->apiUrl/$this->apiVersion/me", [
            'fields' => 'id,name',
            'access_token' => $this->getAccessToken()['access_token']
        ]);

        $data = json_decode($response->body(), true);

        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'username' => '',
            'image' => $this->apiUrl . '/' . $this->apiVersion . '/' . $data['id'] . '/picture?normal',
        ];
    }

    public function getAccount(array $params = []): array
    {
        return $this->getUserAccount();
    }

    public function publish(string $text, array $media, array $params, string $accessToken): array
    {
        $pageId = $params['provider_id'];
        $isVideo = count($media) === 1 && !$media[0]['is_image'];

        // Publish a post in page feed with attached media.
        // `attached_media` = only images support
        if (!$isVideo) {
            $uploadMedia = $this->uploadMediaImages($media, $pageId, $accessToken);

            $postParams = [
                'message' => $text,
                'access_token' => $accessToken
            ];

            if (!empty($uploadMedia)) {
                $postParams['attached_media'] = $uploadMedia;
            }

            $result = Http::post("$this->apiUrl/$this->apiVersion/$pageId/feed", $postParams)->json();
        }

        // Publish as a video post with description
        if ($isVideo) {
            $thumbStream = fopen($media[0]['thumb_path'], 'r');

            $result = $this->uploadVideo(
                filePath: $media[0]['path'],
                targetId: $pageId,
                accessToken: $accessToken,
                meta: [
                    'description' => $text,
                    'thumb' => $thumbStream
                ]);

            if (is_resource($thumbStream)) {
                fclose($thumbStream);
            }
        }

        if (isset($result['error'])) {
            return [
                'errors' => [$result['error']['message']]
            ];
        }

        return [
            'id' => $result['id']
        ];
    }

    public function uploadImage(string $filePath, string $targetId, string $accessToken)
    {
        $stream = fopen($filePath, 'r');

        $result = Http::attach('source', $stream)
            ->post("$this->apiUrl/$this->apiVersion/$targetId/photos", [
                'published' => false,
                'access_token' => $accessToken
            ])->json();

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $result;
    }

    public function uploadVideo(string $filePath, string $targetId, string $accessToken, array $meta = []): array
    {
        $stream = fopen($filePath, 'r');

        // Start
        $session = Http::post("$this->apiUrl/$this->apiVersion/$targetId/videos", [
            'upload_phase' => 'start',
            'file_size' => filesize($filePath),
            'access_token' => $accessToken
        ])->json();

        // Upload chunk
        $startOffset = $session['start_offset'];
        $endOffset = $session['end_offset'];

        do {
            $partialFile = stream_get_contents($stream, ($endOffset - $startOffset), $startOffset);

            $chunk = Http::attach('video_file_chunk', $partialFile, basename($filePath), [
                'Content-Type' => mime_content_type($filePath)
            ])
                ->post("$this->apiUrl/$this->apiVersion/$targetId/videos", [
                    'upload_phase' => 'transfer',
                    'upload_session_id' => $session['upload_session_id'],
                    'start_offset' => $startOffset,
                    'access_token' => $accessToken
                ])->json();

            $startOffset = $chunk['start_offset'];
            $endOffset = $chunk['end_offset'];
        } while ($startOffset !== $endOffset);

        if (is_resource($stream)) {
            fclose($stream);
        }

        // Finish
        $finish = Http::asMultipart()->post("$this->apiUrl/$this->apiVersion/$targetId/videos", array_merge([
            'upload_phase' => 'finish',
            'upload_session_id' => $session['upload_session_id'],
            'access_token' => $accessToken
        ], $meta))->json();

        if (!$finish['success']) {
            return [
                'error' => 'Error uploading video file.'
            ];
        }

        return [
            'id' => $session['video_id']
        ];
    }

    public function uploadMediaImages(array $media, string $targetId, string $accessToken): array
    {
        $ids = [];

        foreach ($media as $item) {
            if ($item['is_image']) {
                $uploadResult = $this->uploadImage($item['path'], $targetId, $accessToken);

                if (isset($uploadResult['id'])) {
                    $ids[] = ['media_fbid' => $uploadResult['id']];
                }
            }
        }

        return $ids;
    }

    public function publishPost(string $text, array $media = [], array $params = [])
    {
    }

    public function deletePost()
    {
    }

    public function getMetrics()
    {
    }
}
