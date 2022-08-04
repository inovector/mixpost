<?php

namespace Lao9s\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Lao9s\Mixpost\Facades\SocialProviderManager;

class AddAccountController extends Controller
{
    public function __invoke(string $providerName): \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse
    {
        $provider = SocialProviderManager::connect($providerName);

        $url = $provider->getAuthUrl();

        if (Request::inertia()) {
            return Inertia::location($url);
        }

        return redirect()->away($url);
    }
}