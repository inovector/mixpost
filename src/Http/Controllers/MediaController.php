<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Inovector\Mixpost\Model\Media;
use Inovector\Mixpost\Resources\MediaResource;

class MediaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Media');
    }

    public function fetch(): AnonymousResourceCollection
    {
        $records = Media::latest('created_at')->simplePaginate(30);

        return MediaResource::collection($records);
    }
}
