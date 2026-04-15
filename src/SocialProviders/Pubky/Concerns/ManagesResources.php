<?php

namespace Inovector\Mixpost\SocialProviders\Pubky\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Inovector\Mixpost\Enums\SocialProviderResponseStatus;
use Inovector\Mixpost\Support\SocialProviderResponse;

trait ManagesResources
{
    public function getAccount(): SocialProviderResponse
    {
        try {
            $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->get("{$this->homeserver}/api/auth/verify");

            return $this->buildResponse($response, function () use ($response) {
                $data = $response->json();
                $publicKey = $this->getAccessToken()['public_key'] ?? '';

                return [
                    'id' => $publicKey,
                    'name' => $data['name'] ?? 'Pubky User',
                    'username' => $publicKey,
                    'image' => $data['avatar'] ?? null,
                    'data' => [
                        'homeserver' => $this->homeserver,
                        'public_key' => $publicKey,
                    ],
                ];
            });
        } catch (\Exception $e) {
            return $this->response(SocialProviderResponseStatus::ERROR, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function publishPost(string $text, Collection $media, array $params = []): SocialProviderResponse
    {
        if ($media->isNotEmpty()) {
            return $this->response(SocialProviderResponseStatus::ERROR, [
                'error' => 'Pubky does not support media uploads. Only text posts are available.',
            ]);
        }

        $postId = Str::uuid()->toString();
        $publicKey = $this->getAccessToken()['public_key'] ?? '';
        
        $postData = [
            'id' => $postId,
            'text' => $text,
            'created_at' => now()->toIso8601String(),
            'author' => $publicKey,
        ];

        try {
            $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->put(
                    "{$this->homeserver}/pub/mixpost/posts/{$postId}",
                    json_encode($postData)
                );

            return $this->buildResponse($response, function () use ($response, $postId, $publicKey) {
                return [
                    'id' => $postId,
                    'url' => "{$this->homeserver}/pub/mixpost/posts/{$postId}",
                ];
            });
        } catch (\Exception $e) {
            return $this->response(SocialProviderResponseStatus::ERROR, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function deletePost($id): SocialProviderResponse
    {
        try {
            $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->delete("{$this->homeserver}/pub/mixpost/posts/{$id}");

            return $this->buildResponse($response);
        } catch (\Exception $e) {
            return $this->response(SocialProviderResponseStatus::ERROR, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getAccountMetrics(): SocialProviderResponse
    {
        return $this->response(SocialProviderResponseStatus::OK, [
            'data' => [],
        ]);
    }
}