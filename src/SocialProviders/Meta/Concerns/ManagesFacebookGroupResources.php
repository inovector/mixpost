<?php

namespace Inovector\Mixpost\SocialProviders\Meta\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Inovector\Mixpost\Enums\SocialProviderResponseStatus;
use Inovector\Mixpost\Support\SocialProviderResponse;
use Intervention\Image\Facades\Image;

trait ManagesFacebookGroupResources
{
    public function getAccount(): SocialProviderResponse
    {
        $response = $this->getEntities();

        if ($response->hasError()) {
            return $response;
        }

        $filter = array_values(Arr::where($response->context(), function ($entity) {
            return $entity['id'] === $this->values['provider_id'];
        }));

        return new SocialProviderResponse(SocialProviderResponseStatus::OK, $filter[0] ?? []);
    }

    public function getEntities(): SocialProviderResponse
    {
        $response = Http::withToken($this->getAccessToken()['access_token'])
            ->get("$this->apiUrl/$this->apiVersion/me/groups", [
                'fields' => 'id,name,cover{source}',
                'admin_only' => true,
                'limit' => 200
            ]);

        $defaultImage = Image::make(__DIR__ . '/../../../../resources/img/facebook-group.jpeg')->encode('data-url')->getEncoded();;

        return $this->buildResponse($response, function () use ($response, $defaultImage) {
            return $response->collect('data')->map(function ($item) use ($defaultImage) {
                return [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'username' => '',
                    'image' => Arr::get($item, 'cover.source', $defaultImage)
                ];
            })->toArray();
        });
    }

    public function publishPost(string $text, Collection $media, array $params = []): SocialProviderResponse
    {
        return parent::publishFacebookPost($text, $media, $params, $this->getAccessToken()['access_token']);
    }

    public function getGroupMetrics(): SocialProviderResponse
    {
        $response = Http::get("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}", [
            'fields' => 'member_count',
            'access_token' => $this->getAccessToken()['access_token']
        ]);

        return $this->buildResponse($response);
    }

    public function deletePost($id): SocialProviderResponse
    {
        return $this->response(SocialProviderResponseStatus::OK, []);
    }
}
