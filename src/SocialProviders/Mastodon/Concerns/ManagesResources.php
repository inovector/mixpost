<?php

namespace Inovector\Mixpost\SocialProviders\Mastodon\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inovector\Mixpost\Enums\SocialProviderResponseStatus;
use Inovector\Mixpost\Support\SocialProviderResponse;

trait ManagesResources
{
    public function getAccount(): SocialProviderResponse
    {
        $response = Http::withToken($this->getAccessToken()['access_token'])
            ->get("$this->serverUrl/api/$this->apiVersion/accounts/verify_credentials");

        return $this->buildResponse($response, function () use ($response) {
            $data = $response->json();

            return [
                'id' => $data['id'],
                'name' => $data['display_name'],
                'username' => $data['username'],
                'image' => $data['avatar'],
                'data' => [
                    'server' => $this->values['data']['server']
                ]
            ];
        });
    }

    public function publishPost(string $text, Collection $media, array $params = []): SocialProviderResponse
    {
        $mediaResponse = $this->uploadMedia($media);

        if ($mediaResponse->hasError()) {
            return $mediaResponse;
        }

        $postParameters = ['status' => $text];

        $ids = $mediaResponse->ids;
        if (!empty($ids)) {
            $postParameters['media_ids'] = $ids;
        }

        $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
            ->withHeaders([
                'Idempotency-Key' => Str::uuid()->toString()
            ])
            ->post("$this->serverUrl/api/$this->apiVersion/statuses", $postParameters);

        return $this->buildResponse($response, function () use ($response) {
            return [
                'id' => $response->json()['id']
            ];
        });
    }

    public function uploadMedia(Collection $media)
    {
        $ids = [];

        foreach ($media->slice(0, 4) as $item) {
            $readStream = $item->readStream();

            $response = $this->buildResponse(
                $this->getHttpClient()::timeout(60 * 10)
                    ->attach('file', $readStream['stream'])
                    ->withToken($this->getAccessToken()['access_token'])
                    ->post("$this->serverUrl/api/$this->apiVersion/media")
            );

            if (is_resource($readStream['stream'])) {
                fclose($readStream['stream']);
            }

            $readStream['temporaryDirectory']?->delete();

            if ($response->hasExceededRateLimit()) {
                return $response;
            }

            if ($response->hasError()) {
                return $response->useContext([
                    "File {$item['name']}: $response->error"
                ]);
            }

            if ($id = $response->id) {
                $ids[] = $id;
            } else {
                return $this->response(SocialProviderResponseStatus::ERROR, ["File {$item['name']}: could not be uploaded to the mastodon server."]);
            }
        }

        return $this->response(SocialProviderResponseStatus::OK, [
            'ids' => $ids
        ]);
    }

    public function getAccountMetrics(): SocialProviderResponse
    {
        $response = Http::withToken($this->getAccessToken()['access_token'])
            ->get("$this->serverUrl/api/$this->apiVersion/accounts/verify_credentials");

        return $this->buildResponse($response);
    }

    public function getUserStatuses(string $userId, string $maxId = ''): SocialProviderResponse
    {
        $params = [
            'exclude_replies' => true,
            'exclude_reblogs' => true,
            'limit' => 40,
        ];

        if ($maxId) {
            $params['max_id'] = $maxId;
        }

        $response = Http::withToken($this->getAccessToken()['access_token'])
            ->get("$this->serverUrl/api/$this->apiVersion/accounts/$userId/statuses", $params);

        return $this->buildResponse($response, function () use ($response) {
            return [
                'data' => $response->json()
            ];
        });
    }

    public function deletePost($id): SocialProviderResponse
    {
        return $this->response(SocialProviderResponseStatus::OK, []);
    }
}
