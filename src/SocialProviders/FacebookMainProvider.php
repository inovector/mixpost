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
