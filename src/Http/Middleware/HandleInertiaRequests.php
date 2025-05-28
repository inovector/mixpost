<?php

namespace Inovector\Mixpost\Http\Middleware;

use Composer\InstalledVersions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Middleware;
use Inovector\Mixpost\Concerns\UsesAuth;
use Inovector\Mixpost\Facades\Settings;
use Inovector\Mixpost\Http\Resources\UserResource;
use Inovector\Mixpost\Models\User;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    use UsesAuth;

    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'mixpost::app';

    /**
     * Determine the current asset version.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function version(Request $request)
    {
        if (file_exists($manifest = public_path('vendor/mixpost/manifest.json'))) {
            return md5_file($manifest);
        }

        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => $this->auth(),
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'warning' => $request->session()->get('warning'),
                    'error' => $request->session()->get('error'),
                    'info' => $request->session()->get('info'),
                ];
            },
            'app' => [
                'name' => Config::get('app.name'),
                'horizon_path' => Config::get('horizon.path'),
            ],
            'mixpost' => [
                'docs_link' => 'https://docs.mixpost.app',
                'version' => InstalledVersions::getVersion('inovector/mixpost'),
                'mime_types' => Config::get('mixpost.mime_types'),
                'settings' => [
                    'timezone' => Settings::get('timezone'),
                    'time_format' => Settings::get('time_format'),
                    'week_starts_on' => Settings::get('week_starts_on'),
                ]
            ]
        ]);
    }

    protected function auth(): array
    {
        if (!self::getAuthGuard()->check()) {
            return [
                'user' => null
            ];
        }

        $user = self::getAuthGuard()->user();

        // If `Auth Middleware` was not resolved first
        // return empty auth
        if (!$user instanceof User) {
            return [];
        }

        return [
            'user' => new UserResource($user),
        ];
    }
}
