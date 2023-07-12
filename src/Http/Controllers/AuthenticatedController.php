<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestInertia;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inovector\Mixpost\Concerns\UsesAuth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatedController extends Controller
{
    use UsesAuth;

    public function destroy(Request $request): Response|RedirectResponse
    {
        self::getAuthGuard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if (RequestInertia::inertia()) {
            return Inertia::location(route(config('mixpost.redirect_unauthorized_users_to_route')));
        }

        return redirect()->away(route(config('mixpost.redirect_unauthorized_users_to_route')));
    }
}
