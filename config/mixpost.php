<?php

return [
    /*
    * This option controls the default authentication "guard" for the Mixpost routes
    */
    'auth_guard' => env('MIXPOST_AUTH_GUARD', 'web'),

    /*
    * If you use another model for users, you can change it here.
    */
    'user_model' => \Inovector\Mixpost\Models\User::class,

    /*
     * Mixpost will redirect unauthorized users to the route name specified here
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
        'video' => 1024 * 200 // 200MB
    ],

    /*
     * Accepted mime types for media library upload.
     * These are all supported mime types for the image and video files. We do not guarantee that it will work with other types.
     * If you need to remove certain mime types, you are free to do so from here.
     */
    'mime_types' => [
        'image/jpg',
        'image/jpeg',
        'image/gif',
        'image/png',
        'video/mp4',
        'video/x-m4v'
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

    /*
     * Define cache prefix
     */
    'cache_prefix' => env('MIXPOST_CACHE_PREFIX', 'mixpost'),

    /*
     * Define log channel
     * Captures connection errors with social networks or third parties used in Mixpost in a separate channel.
     * Leave blank if you want to use Laravel's default log channel
     */
    'log_channel' => env('MIXPOST_LOG_CHANNEL'),

    /*
     * The media component is integrated with third-party services Unsplash.com and Tenor.com
     * Defines the default terms for displaying media resources
     */
    'external_media_terms' => ['social', 'mix', 'content', 'popular', 'viral', 'trend', 'light', 'marketing', 'self-hosted', 'ambient', 'writer', 'technology'],

    /*
     * Options for each social network
     * We recommend leaving these options unchanged
     * You only change them when the API policy of the social networks changes, and you know what you are doing.
     */
    'social_provider_options' => [
        'twitter' => [
            'simultaneous_posting_on_multiple_accounts' => false,
            'post_character_limit' => 280,
            'media_limit' => [
                'photos' => 4,
                'videos' => 1,
                'gifs' => 1,
                'allow_mixing' => false,
            ]
        ],
        'facebook_page' => [
            'simultaneous_posting_on_multiple_accounts' => true,
            'post_character_limit' => 5000,
            'media_limit' => [
                'photos' => 10,
                'videos' => 1,
                'gifs' => 1,
                'allow_mixing' => false,
            ]
        ],
        'facebook_group' => [
            'simultaneous_posting_on_multiple_accounts' => true,
            'post_character_limit' => 5000,
            'media_limit' => [
                'photos' => 10,
                'videos' => 1,
                'gifs' => 1,
                'allow_mixing' => false,
            ]
        ],
        'mastodon' => [
            'simultaneous_posting_on_multiple_accounts' => true,
            'post_character_limit' => 500,
            'media_limit' => [
                'photos' => 4,
                'videos' => 1,
                'gifs' => 1,
                'allow_mixing' => false,
            ]
        ]
    ],
];
