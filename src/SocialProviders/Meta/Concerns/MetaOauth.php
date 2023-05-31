<?php

namespace Inovector\Mixpost\SocialProviders\Meta\Concerns;

trait MetaOauth
{
    public function requestAccessToken(array $params = []): array
    {
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $params['code'],
            'redirect_uri' => $this->redirectUrl,
        ];

        $response = $this->getHttpClient()::post("$this->apiUrl/$this->apiVersion/oauth/access_token", $params)->json();

        if (isset($response['error'])) {
            return [
                'error' => $response['error']['message']
            ];
        }

        return $this->requestLongLivedAccessToken($response['access_token']);
    }

    public function requestLongLivedAccessToken(string $shortLivedAccessToken = '')
    {
        $params = [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'fb_exchange_token' => $shortLivedAccessToken ?: $this->getAccessToken()['access_token']
        ];

        return $this->getHttpClient()::post("$this->apiUrl/$this->apiVersion/oauth/access_token", $params)->json();
    }
}
