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

    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $values = [])
    {
        $this->serverUrl = "https://{$values['data']['server']}";

        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $values);
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

        $result = Http::post("$this->serverUrl/oauth/token", $params)->json();

        return [
            'access_token' => $result['access_token']
        ];
    }

    public function getAccount(): array
    {
        $result = Http::withToken($this->getAccessToken()['access_token'])
            ->get("$this->serverUrl/api/$this->apiVersion/accounts/verify_credentials")
            ->json();

        return [
            'id' => $result['id'],
            'name' => $result['display_name'],
            'username' => $result['username'],
            'image' => $result['avatar'],
            'data' => [
                'server' => $this->values['data']['server']
            ]
        ];
    }

    public function publishPost(string $text, array $media = [], array $params = []): array
    {
        $mediaResult = $this->uploadMedia($media);

        if (!empty($mediaResult['errors'])) {
            return [
                'errors' => $mediaResult['errors']
            ];
        }

        $postParameters = ['status' => $text];

        if (!empty($mediaResult['ids'])) {
            $postParameters['media_ids'] = $mediaResult['ids'];
        }

        $result = Http::withToken($this->getAccessToken()['access_token'])
            ->withHeaders([
                'Idempotency-Key' => Str::uuid()->toString()
            ])
            ->post("$this->serverUrl/api/$this->apiVersion/statuses", $postParameters)
            ->json();

        if (isset($result['error'])) {
            return [
                'errors' => [$result['error']]
            ];
        }

        return [
            'id' => $result['id']
        ];
    }

    public function uploadMedia(array $media): array
    {
        $ids = [];
        $errors = [];

        foreach (array_slice($media, 0, 4) as $item) {
            $stream = fopen($item['path'], 'r');

            $result = Http::attach('file', $stream)
                ->timeout(60 * 5)
                ->withToken($this->getAccessToken()['access_token'])
                ->post("$this->serverUrl/api/$this->apiVersion/media")
                ->json();

            if (is_resource($stream)) {
                fclose($stream);
            }

            if (isset($result['error'])) {
                $errors[] = $result['error'];
                continue;
            }

            $ids[] = $result['id'];
        }

        return [
            'ids' => $ids,
            'errors' => $errors
        ];
    }

    public function getAccountMetrics(): array
    {
        $request = Http::withToken($this->getAccessToken()['access_token'])
            ->get("$this->serverUrl/api/$this->apiVersion/accounts/verify_credentials");

        if ($request->status() !== 200) {
            return $this->buildErrorResponse($request->status(), $request->json('error'));
        }

        $result = $request->json();

        return [
            'followers_count' => $result['followers_count'],
            'following_count' => $result['following_count'],
            'statuses_count' => $result['statuses_count'],
        ];
    }

    public function getAccountStatuses(string $userId, string $maxId = ''): array
    {
        $params = [
            'exclude_replies' => true,
            'exclude_reblogs' => true,
            'limit' => 40,
        ];

        if ($maxId) {
            $params['max_id'] = $maxId;
        }

        $request = Http::withToken($this->getAccessToken()['access_token'])->get("$this->serverUrl/api/$this->apiVersion/accounts/$userId/statuses", $params);

        if ($request->status() !== 200) {
            return $this->buildErrorResponse($request->status(), $request->json('error'));
        }

        return $request->json();
    }

    public function deletePost()
    {
        // TODO: Implement deletePost() method.
    }
}
