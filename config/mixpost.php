<?php

return [
    /*
     * Credentials for third-party services
     */
    'credentials' => [
        'twitter' => [
            'client_id' => env('MIXPOST_TWITTER_CLIENT_ID'),
            'client_secret' => env('MIXPOST_TWITTER_CLIENT_SECRET'),
        ],
        'facebook' => [
            'client_id' => env('MIXPOST_FACEBOOK_CLIENT_ID'),
            'client_secret' => env('MIXPOST_FACEBOOK_CLIENT_SECRET')
        ],
        'mastodon' => [
            'client_id' => env('MIXPOST_MASTODON_CLIENT_ID'),
            'client_secret' => env('MIXPOST_MASTODON_CLIENT_SECRET')
        ],
        'unsplash' => [
            'client_id' => env('MIXPOST_UNSPLASH_CLIENT_ID')
        ],
        'tenor' => [
            'client_id' => env('MIXPOST_TENOR_CLIENT_ID')
        ],
    ],

    /*
     * Rules for each social network
     * We recommend leaving these options unchanged
     * You only change them when the API policy of the social networks changes, and you know what you are doing.
     */
    'social_provider_rules' => [
        'twitter' => [
            'simultaneous_posting_on_multiple_accounts' => false,
            'post_characters_limit' => 280,
            'simultaneous_posting_photo_video' => false // GIFs also considered as video
        ],
        'facebook_page' => [
            'simultaneous_posting_on_multiple_accounts' => true,
            'post_characters_limit' => null,
            'simultaneous_posting_photo_video' => false // GIFs also considered as video
        ],
        'facebook_group' => [
            'simultaneous_posting_on_multiple_accounts' => true,
            'post_characters_limit' => null,
            'simultaneous_posting_photo_video' => false // GIFs also considered as video
        ],
        'mastodon' => [
            'simultaneous_posting_on_multiple_accounts' => true,
            'post_characters_limit' => null,
            'simultaneous_posting_photo_video' => false // GIFs also considered as video
        ]
    ],

    /**
     * Mixpost will redirect unauthorized users to the route specified here
     */
    'redirect_unauthorized_users_to_route' => 'login',

    /*
     * The disk on which to store added files.
     * Choose one or more of the disks you've configured in config/filesystems.php.
     */
    'disk' => env('MIXPOST_DISK', 'public'),

    /*
     * Indicate that the uploaded file should be no more than the given number of kilobytes.
     * Adding a larger file will result in an exception.
     */
    'max_file_size' => [
        'image' => 1024 * 5, // 5MB
        'gif' => 1024 * 15, // 15MB
        'video' => 1024 * 500 // 500MB
    ],

    /**
     * Accepted mime types for media library upload.
     * These are all supported mime types for the image and video files. We do not guarantee that it will work with other types.
     * If you need to remove certain mime types, you are free to do so from here.
     */
    'mime_types' => [
        'image/jpg',
        'image/jpeg',
        'image/gif',
        'image/png',
        'video/mp4'
    ],

    /*
     * The path where to store temporary files while performing image conversions.
     * If set to null, storage_path('mixpost-media/temp') will be used.
     */
    'temporary_directory_path' => null,

    /*
     * FFMPEG & FFProbe binaries paths, only used if you try to generate video thumbnails
     */
    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),

    /**
     * Define cache prefix
     */
    'cache_prefix' => env('MIXPOST_CACHE_PREFIX', 'mixpost'),

    /**
     * The media component is integrated with third-party services Unsplash.com and Tenor.com
     * Defines the default terms for displaying media resources
     */
    'external_media_terms' => ['young', 'social', 'mix', 'content', 'viral', 'trend', 'test', 'light', 'true', 'false', 'marketing', 'self-hosted', 'ambient', 'writer', 'technology']
];
