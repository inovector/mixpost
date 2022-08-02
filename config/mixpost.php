<?php

return [
    'credentials' => [
        'twitter' => [
            'client_id' => env('MIXPOST_TWITTER_CLIENT_ID'),
            'client_secret' => env('MIXPOST_TWITTER_CLIENT_SECRET'),
            'redirect' => env('MIXPOST_TWITTER_REDIRECT', 'http://localhost/mixpost/callback/twitter')
        ],
    ]
];
