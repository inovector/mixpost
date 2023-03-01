<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use \Illuminate\Http\Response as HttpResponse;
use Inovector\Mixpost\Facades\Services;
use Inovector\Mixpost\Http\Requests\DeleteMedia;

class MediaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Media', [
            'has_service' => [
                'unsplash' => !!Services::get('unsplash', 'client_id'),
                'tenor' => !!Services::get('tenor', 'client_id')
            ]
        ]);
    }

    public function destroy(DeleteMedia $deleteMediaFiles): HttpResponse
    {
        $deleteMediaFiles->handle();

        return response()->noContent();
    }
}
