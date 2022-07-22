<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth overflow-x-hidden">
<head>
    <title inertia>Mixpost{{ config('app.name') ? ' - ' . config('app.name') : '' }}</title>
    <meta name="robots" content="noindex, nofollow">
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800&amp;display=swap" rel="stylesheet">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{--    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">--}}
    {{--    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">--}}
    {{--    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">--}}
    {{--    <link rel="manifest" href="/favicon/site.webmanifest">--}}
    @routes
    {{ mixpostAssets() }}
    @inertiaHead
</head>
<body class="antialiased font-sans">
@inertia
</body>
</html>
