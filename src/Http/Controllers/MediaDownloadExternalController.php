<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Inovector\Mixpost\Http\Requests\MediaDownloadExternal;
use Inovector\Mixpost\Http\Resources\MediaResource;

class MediaDownloadExternalController extends Controller
{
    public function __invoke(MediaDownloadExternal $downloadMedia): array
    {
        $media = $downloadMedia->handle();

        return MediaResource::collection($media)->resolve();
    }
}
