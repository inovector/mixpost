<?php

namespace Inovector\Mixpost\Http\Controllers;

use Composer\InstalledVersions;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Inovector\Mixpost\Support\HorizonStatus;
use Inovector\Mixpost\Util;

class SystemStatusController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('System/Status', [
            'health' => [
                'env' => App::environment(),
                'debug' => Config::get('app.debug'),
                'horizon_status' => resolve(HorizonStatus::class)->get(),
                'has_queue_connection' => Config::get('queue.connections.mixpost-redis') && !empty(Config::get('queue.connections.mixpost-redis')),
                'last_scheduled_run' => $this->getLastScheduleRun(),
            ],
            'tech' => [
                'cache_driver' => Config::get('cache.default'),
                'base_path' => base_path(),
                'disk' => Config::get('mixpost.disk'),
                'log_channel' => Config::get('mixpost.log_channel') ? Config::get('mixpost.log_channel') : Config::get('logging.default'),
                'user_agent' => $request->userAgent(),
                'ffmpeg_status' => Util::isFFmpegInstalled() ? 'Installed' : 'Not Installed',
                'versions' => [
                    'php' => PHP_VERSION,
                    'laravel' => App::version(),
                    'horizon' => InstalledVersions::getVersion('laravel/horizon'),
                    'mysql' => $this->mysqlVersion(),
                    'mixpost' => InstalledVersions::getVersion('inovector/mixpost'),
                ]
            ],
        ]);
    }

    protected function getLastScheduleRun(): array
    {
        $lastScheduleRun = Cache::get('mixpost-last-schedule-run');

        if (!$lastScheduleRun) {
            return [
                'variant' => 'error',
                'message' => 'It never started'
            ];
        }

        $diff = (int)abs(Carbon::now('UTC')->diffInMinutes($lastScheduleRun));

        if ($diff < 10) {
            return [
                'variant' => 'success',
                'message' => "Ran $diff minute(s) ago"
            ];
        }

        return [
            'variant' => 'warning',
            'message' => "Ran $diff minute(s) ago"
        ];
    }

    protected function mysqlVersion(): string
    {
        if (!Util::isMysqlDatabase()) {
            return '';
        }

        $results = DB::select('select version() as version');

        return (string)$results[0]->version;
    }
}
