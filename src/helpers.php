<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Arr;
use Inovector\Mixpost\Facades\Settings;

if (!function_exists('mixpostAssets')) {
    function mixpostAssets(): HtmlString
    {
        $hot = __DIR__ . '/../resources/dist/hot';

        $devServerIsRunning = file_exists($hot);

        if ($devServerIsRunning) {
            $viteServer = file_get_contents($hot);

            return new HtmlString(<<<HTML
                <script type="module" src="$viteServer/@vite/client"></script>
                <script type="module" src="$viteServer/resources/js/app.js"></script>
            HTML
            );
        }

        $manifest = json_decode(file_get_contents(
            public_path('vendor/mixpost/manifest.json')
        ), true);

        return new HtmlString(<<<HTML
                <script type="module" src="/vendor/mixpost/{$manifest['resources/js/app.js']['file']}"></script>
                <link rel="stylesheet" href="/vendor/mixpost/{$manifest['resources/js/app.js']['css'][0]}">
            HTML
        );
    }
}

if (!function_exists('socialProviderRules')) {
    function socialProviderRules($provider)
    {
        $items = config('mixpost.social_provider_rules');

        return Arr::get($items, $provider);
    }
}

if (!function_exists('removeHtmlTags')) {
    function removeHtmlTags($string): string
    {
        $text = trim(strip_tags($string));

        return html_entity_decode($text);
    }
}

if (!function_exists('convertTimeToUTC')) {
    function convertTimeToUTC(string|DateTimeInterface|null $time = null, DateTimeZone|string|null $tz = null): Carbon
    {
        return Carbon::parse($time, $tz ?: Settings::get('timezone'))->timezone('UTC');
    }
}

if (!function_exists('timeFormat')) {
    function timeFormat(): string
    {
        return Settings::get('time_format') == 24 ? 'H:i' : 'h:ia';
    }
}
