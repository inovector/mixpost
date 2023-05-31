<?php

namespace Inovector\Mixpost\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inovector\Mixpost\Models\User;
use Inovector\Mixpost\Concerns\UsesAuth;
use Inovector\Mixpost\Concerns\UsesUserModel;
use Symfony\Component\HttpFoundation\Response;

class Auth
{
    use UsesUserModel;
    use UsesAuth;

    public function handle(Request $request, Closure $next)
    {
        AuthFacade::shouldUse(self::getAuthGuardName());

        if (!auth()->check()) {
            return $this->redirect($request);
        }

        if (!Gate::allows('viewMixpost')) {
            abort(403);
        }

        // TODO: find a better way to use the custom model instance
        if (!auth()->user() instanceof User) {
            $user = self::getUserClass()::make(auth()->user()->only('name', 'email'))->setAttribute('id', auth()->id());

            AuthFacade::setUser($user);
        }

        return $next($request);
    }

    protected function redirect(Request $request): JsonResponse|Response
    {
        if (!$request->expectsJson()) {
            $request->session()->put('url.intended', url()->current());

            return Inertia::location(route(config('mixpost.redirect_unauthorized_users_to_route')));
        }

        return response()->json('Unauthenticated.', Response::HTTP_UNAUTHORIZED);
    }
}
