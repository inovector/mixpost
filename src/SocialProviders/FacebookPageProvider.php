<?php

namespace Inovector\Mixpost\SocialProviders;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class FacebookPageProvider extends FacebookMainProvider
{
    public bool $onlyUserAccount = false;

    public function getAuthUrl(): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => 'public_profile,pages_show_list,pages_read_engagement,pages_manage_posts',
            'response_type' => 'code',
            'state' => null
        ];

        $url = 'https://www.facebook.com/' . $this->apiVersion . '/dialog/oauth';

        return $this->buildUrlFromBase($url, $params);
    }

    public function getAccount(array $params = []): array
    {
        $filter = array_values(Arr::where($this->getEntities(), function ($entity) use ($params) {
            return $entity['id'] === $params['provider_id'];
        }));

        return $filter[0] ?? [];
    }

    public function getEntities(bool $withAccessToken = false): array
    {
        $result = Http::get("$this->apiUrl/$this->apiVersion/me/accounts", [
            'fields' => 'id,name,username,picture{url}' . ($withAccessToken ? ',access_token' : ''),
            'access_token' => $this->getAccessToken()['access_token']
        ])->collect('data');

        return $result->map(function ($item) use ($withAccessToken) {
            $array = [
                'id' => $item['id'],
                'name' => $item['name'],
                'username' => $item['username'] ?? '',
                'image' => Arr::get($item, 'picture.data.url')
            ];

            if ($withAccessToken) {
                $array['access_token'] = [
                    'access_token' => $item['access_token']
                ];
            }

            return $array;
        })->toArray();
    }

    public function publishPost(string $text, array $media = [], array $params = [])
    {
        return parent::publish($text, $media, $params, $this->getAccessToken()['page_access_token']);
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
