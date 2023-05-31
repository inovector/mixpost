<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inovector\Mixpost\Concerns\UsesAuth;

class AuthenticatedController extends Controller
{
    use UsesAuth;

    public function destroy(Request $request): RedirectResponse
    {
        self::getAuthGuard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route(config('mixpost.redirect_unauthorized_users_to_route'));
    }
}
