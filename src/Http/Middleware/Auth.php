<?php

namespace Inovector\Mixpost\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class Auth
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $this->redirect($request);
        }

        if (!Gate::allows('viewMixpost')) {
            abort(403);
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
