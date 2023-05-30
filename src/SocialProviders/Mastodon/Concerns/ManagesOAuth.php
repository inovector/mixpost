<?php

namespace Inovector\Mixpost\SocialProviders\Mastodon\Concerns;

use Illuminate\Support\Facades\Http;

trait ManagesOAuth
{
    public function getAuthUrl(): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => 'read write',
            'response_type' => 'code',
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
}
