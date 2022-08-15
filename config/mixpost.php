<?php

return [
    'credentials' => [
        'twitter' => [
            'client_id' => env('MIXPOST_TWITTER_CLIENT_ID'),
            'client_secret' => env('MIXPOST_TWITTER_CLIENT_SECRET'),
            'redirect' => env('MIXPOST_TWITTER_REDIRECT', 'http://localhost/mixpost/callback/twitter')
        ],
    ],

    'social_provider_options' => [
        'twitter' => [
            'simultaneous_posting_on_multiple_accounts' => false,
            'post_characters_limit' => 280
        ],
        'facebook' => [
            'simultaneous_posting_on_multiple_accounts' => true,
            'post_characters_limit' => null
        ]
    ]
];
