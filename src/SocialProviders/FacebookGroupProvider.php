<?php

namespace Inovector\Mixpost\SocialProviders;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class FacebookGroupProvider extends FacebookMainProvider
{
    public bool $onlyUserAccount = false;

    public function getAuthUrl(): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => 'public_profile,publish_to_groups,groups_access_member_info',
            'response_type' => 'code',
            'state' => null
        ];

        $url = 'https://www.facebook.com/' . $this->apiVersion . '/dialog/oauth';

        return $this->buildUrlFromBase($url, $params);
    }

    public function getAccount(): array
    {
        $filter = array_values(Arr::where($this->getEntities(), function ($entity) {
            return $entity['id'] === $this->values['provider_id'];
        }));

        return $filter[0] ?? [];
    }

    public function getEntities(): array
    {
        $result = Http::get("$this->apiUrl/$this->apiVersion/me/groups", [
            'fields' => 'id,name,cover{source}',
            'access_token' => $this->getAccessToken()['access_token']
        ])->collect('data');

        return $result->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'username' => '',
                'image' => Arr::get($item, 'cover.source')
            ];
        })->toArray();
    }

    public function publishPost(string $text, array $media = [], array $params = [])
    {
        return parent::publish($text, $media, $this->getAccessToken()['access_token']);
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
