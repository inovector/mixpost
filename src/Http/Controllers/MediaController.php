<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Response as HttpResponse;
use Inovector\Mixpost\Enums\ServiceGroup;
use Inovector\Mixpost\Facades\ServiceManager;
use Inovector\Mixpost\Http\Requests\DeleteMedia;

class MediaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Media', [
            'is_configured_service' => ServiceManager::isActive(
                ServiceManager::services()->group(ServiceGroup::MEDIA)->getNames()
            )
        ]);
    }

    public function destroy(DeleteMedia $deleteMediaFiles): HttpResponse
    {
        $deleteMediaFiles->handle();

        return response()->noContent();
    }
}
