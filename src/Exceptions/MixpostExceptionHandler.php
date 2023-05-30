<?php

namespace Inovector\Mixpost\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inovector\Mixpost\Util;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class MixpostExceptionHandler extends ExceptionHandler
{
    public function render($request, Throwable $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        if (Util::isMixpostRequest($request)) {
            return $this->renderInertiaException($request, $this->prepareException($e));
        }

        return parent::render($request, $e);
    }

    protected function renderInertiaException($request, $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : ($e->status ?? 500);

        Inertia::setRootView('mixpost::app');

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

        if ($statusCode === 500 && !App::hasDebugModeEnabled()) {
            return Inertia::render('ErrorPage', [
                'title' => 'Internal error',
                'text' => 'An internal error has occurred.'
            ])->toResponse($request)->setStatusCode(500);
        }

        return parent::render($request, $e);
    }
}
