<?php

namespace Inovector\Mixpost\SocialProviders;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class FacebookPageProvider extends FacebookMainProvider
{
    public bool $onlyUserAccount = false;

    public function getAccount(): array
    {
        $filter = array_values(Arr::where($this->getEntities(), function ($entity) {
            return $entity['id'] === $this->values['provider_id'];
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
        return parent::publish($text, $media, $this->getAccessToken()['page_access_token']);
    }

    public function getPageAudience(): array
    {
        $result = Http::get("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}", [
            'fields' => 'fan_count,followers_count',
            'access_token' => $this->getAccessToken()['page_access_token']
        ])->json();

        if (isset($result['error'])) {
            return $this->buildErrorResponse($result['error']['code'], $result['error']['message']);
        }

        return [
            'followers_count' => $result['followers_count'],
            'fan_count' => $result['fan_count']
        ];
    }

    public function getPageInsights()
    {
        $data = [
            'access_token' => $this->getAccessToken()['page_access_token'],
            'metric' => 'page_engaged_users,page_post_engagements,page_posts_impressions',
            'period' => 'day',
            'since' => Carbon::now()->subDays(90)->toDateString(),
            'until' => Carbon::now()->toDateString(),
        ];

        $result = Http::get("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/insights", $data)->json();

        if (isset($result['error'])) {
            return $this->buildErrorResponse($result['error']['code'], $result['error']['message']);
        }

        return $result;
    }

    public function deletePost()
    {
        // TODO: Implement deletePost() method.
    }
}
