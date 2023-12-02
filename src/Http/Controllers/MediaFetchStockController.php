<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Inovector\Mixpost\Http\Resources\MediaResource;
use Inovector\Mixpost\Integrations\Unsplash\Unsplash;
use Inovector\Mixpost\Models\Media;

class MediaFetchStockController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $unsplash = new Unsplash();

        $items = $unsplash->photos($request->query('keyword', ''), $request->query('page', 1));

        $media = collect($items)->map(function ($item) {
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
            $media->setAttribute('download_data', [
                'download_location' => $item['links']['download_location']
            ]);

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
