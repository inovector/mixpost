<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Inovector\Mixpost\Http\Resources\MediaResource;
use Inovector\Mixpost\Models\Media;

class FetchMediaGifsController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $clientId = config('mixpost.credentials.tenor.client_id');

        $words = ['young', 'social', 'mix', 'content', 'viral', 'trend', 'test', 'light', 'true', 'false', 'marketing', 'self-hosted', 'ambient', 'writer', 'technology'];

        $items = Http::get("https://tenor.googleapis.com/v2/search", [
            'client_key' => $clientId,
            'key' => $clientId,
            'q' => $request->query('keyword', Arr::random($words)),
            'limit' => 30,
        ]);

        $media = collect($items->json('results', []))->map(function ($item) {
            $media = new Media([
                'name' => $item['content_description'],
                'mime_type' => 'image/gif',
                'disk' => 'stock',
                'path' => $item['media_formats']['tinygif']['url'],
                'conversions' => [
                    [
                        'disk' => 'stock',
                        'name' => 'thumb',
                        'path' => $item['media_formats']['gif']['url']
                    ]
                ]
            ]);

            $media->setAttribute('id', $item['id']);

            return $media;
        });

        $nextPage = intval($request->get('page', 1)) + 1;

        return MediaResource::collection($media)->additional([
            'links' => [
                'next' => "?page=$nextPage"
            ]
        ]);
    }
}
