<?php

use Illuminate\Support\HtmlString;

if (! function_exists('mixpostAssets')) {
    function mixpostAssets(): HtmlString
    {
        $hot = __DIR__.'/../resources/dist/hot';

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
