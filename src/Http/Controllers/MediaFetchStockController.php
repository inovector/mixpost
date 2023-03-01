<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Inovector\Mixpost\Facades\Services;
use Inovector\Mixpost\Http\Resources\MediaResource;
use Inovector\Mixpost\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class MediaFetchStockController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $clientId = Services::get('unsplash', 'client_id');

        if (!$clientId) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $terms = config('mixpost.external_media_terms');

        $items = Http::get("https://api.unsplash.com/search/photos", [
            'client_id' => $clientId,
            'query' => $request->query('keyword', Arr::random($terms)),
            'page' => $request->query('page', 1),
            'per_page' => 30,
        ]);

        $media = collect($items->json('results', []))->map(function ($item) {
            $media = new Media([
                'name' => $item['user']['name'],
                'mime_type' => 'image/jpeg',
                'disk' => 'external_media',
                'path' => $item['urls']['regular'],
                'conversions' => [
                    [
                        'disk' => 'stock',
                        'name' => 'thumb',
                        'path' => $item['urls']['thumb']
                    ]
                ]
            ]);

            $media->setAttribute('id', $item['id']);
            $media->setAttribute('credit_url', $item['user']['links']['html']);

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
