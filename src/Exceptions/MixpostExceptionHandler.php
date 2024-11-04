<?php

namespace Inovector\Mixpost\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inovector\Mixpost\Http\Middleware\HandleInertiaRequests;
use Inovector\Mixpost\Util;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class MixpostExceptionHandler extends ExceptionHandler
{
    public function render($request, Throwable $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        if (Util::isMixpostRequest($request)) {
            if (!$request->wantsJson()) {
                return $this->renderInertiaException($request, $this->prepareException($e));
            }

            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => class_basename($e->getModel()) . ' not found.'
                ], 404);
            }
        }

        return parent::render($request, $e);
    }

    protected function renderInertiaException($request, $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : ($e->status ?? 500);

        $shared = (new HandleInertiaRequests())->share($request);

        Inertia::setRootView('mixpost::app');
        Inertia::share($shared);

        if ($statusCode === 403) {
            return Inertia::render('ErrorPage', [
                'title' => 'Access forbidden!',
                'text' => 'You do not have access to this page.'
            ])->toResponse($request)->setStatusCode($statusCode);
        }

        if ($statusCode === 404) {
            return Inertia::render('ErrorPage', [
                'title' => '404 - Whoops...',
                'text' => "The page you are trying to view does not exist."
            ])->toResponse($request)->setStatusCode($statusCode);
        }

        if ($statusCode === 419) {
            return Inertia::render('Main/ErrorPage', [
                'title' => '419',
                'text' => "The page has expired due to inactivity."
            ])->toResponse($request)->setStatusCode($statusCode);
        }

        if ($statusCode === 500 && !App::hasDebugModeEnabled()) {
            return Inertia::render('ErrorPage', [
                'title' => 'Internal error',
                'text' => 'An internal error has occurred.'
            ])->toResponse($request)->setStatusCode(500);
        }

        return parent::render($request, $e);
    }
}
