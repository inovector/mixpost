<?php

namespace Inovector\Mixpost\Integrations\Unsplash;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Inovector\Mixpost\Services\UnsplashService;
use Inovector\Mixpost\Util;

class Unsplash
{
    protected string $clientId;
    protected string $endpointUrl = 'https://api.unsplash.com';

    public function __construct()
    {
        $clientId = UnsplashService::getConfiguration('client_id');

        if (!$clientId) {
            throw new \Exception('Unsplash is not configured.');
        }

        $this->clientId = $clientId;
    }

    public function photos(string $query = '', int $page = 1): array
    {
        return Http::get("$this->endpointUrl/search/photos", [
            'client_id' => $this->clientId,
            'query' => $query ?: Arr::random(Util::config('external_media_terms')),
            'page' => $page,
            'per_page' => 30,
        ])->json('results', []);
    }

    public function downloadPhoto(string $downloadLocation)
    {
        $download_path = parse_url($downloadLocation, PHP_URL_PATH);
        $download_query = parse_url($downloadLocation, PHP_URL_QUERY);

        return Http::get("$this->endpointUrl$download_path?$download_query", [
            'client_id' => $this->clientId,
        ])->json();
    }
}
