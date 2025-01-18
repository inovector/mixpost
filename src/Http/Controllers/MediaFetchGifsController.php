<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inovector\Mixpost\Http\Resources\MediaResource;
use Inovector\Mixpost\Models\Media;
use Inovector\Mixpost\Services\TenorService;
use Symfony\Component\HttpFoundation\Response;

class MediaFetchGifsController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $clientId = TenorService::getConfiguration('client_id');

        if (!$clientId) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $terms = config('mixpost.external_media_terms');

        $items = Http::get("https://tenor.googleapis.com/v2/search", [
            'key' => $clientId,
            'client_key' => Str::slug(Config::get('app.name', 'mixpost'), '_'),
            'q' => $request->query('keyword', Arr::random($terms)),
            'limit' => 30,
        ]);

        $media = collect($items->json('results', []))->map(function ($item) {
            $media = new Media([
                'name' => $item['content_description'],
                'mime_type' => 'image/gif',
                'disk' => 'external_media',
                'path' => $item['media_formats']['tinygif']['url'],
                'conversions' => [
                    [
                        'disk' => 'stock',
                        'name' => 'thumb',
                        'path' => $item['media_formats']['tinygif']['url']
                    ]
                ]
            ]);

            $media->setAttribute('id', $item['id']);
            $media->setAttribute('download_data', 'false');

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
