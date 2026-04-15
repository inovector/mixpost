<?php

namespace Inovector\Mixpost\SocialProviders\Pubky\Concerns;

use Illuminate\Support\Facades\Http;
use Inovector\Mixpost\Enums\SocialProviderResponseStatus;
use Inovector\Mixpost\Support\SocialProviderResponse;
use Inovector\Mixpost\Util;

trait ManagesOAuth
{
    public function getAuthUrl(): string
    {
        return "{$this->homeserver}/signup";
    }

    public function requestAccessToken(array $params = []): array
    {
        $secret = $params['secret'] ?? null;
        $publicKey = $params['public_key'] ?? null;

        if (!$secret || !$publicKey) {
            return [
                'error' => 'Missing key credentials',
            ];
        }

        $response = $this->getHttpClient()::withToken($secret)
            ->get("{$this->homeserver}/api/auth/verify");

        if ($response->failed()) {
            return [
                'error' => 'Invalid credentials',
            ];
        }

        return [
            'access_token' => $secret,
            'public_key' => $publicKey,
        ];
    }

    public function generateKeypair(): array
    {
        $keypair = sodium_crypto_box_keypair();
        
        return [
            'public_key' => sodium_bin2hex(sodium_crypto_box_publickey($keypair)),
            'secret' => sodium_bin2hex(sodium_crypto_box_secretkey($keypair)),
        ];
    }

    public function verifyCredentials(string $secret, string $publicKey): SocialProviderResponse
    {
        try {
            $response = Http::withToken($secret)
                ->get("{$this->homeserver}/api/auth/verify");

            return $this->buildResponse($response, function () use ($response, $publicKey) {
                $data = $response->json();
                
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
}