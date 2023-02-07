<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth overflow-x-hidden">
<head>
    <title inertia>Mixpost{{ config('app.name') ? ' - ' . config('app.name') : '' }}</title>
    <meta name="robots" content="noindex, nofollow">
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800&amp;display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('/vendor/mixpost/favicon.ico') }}">
    @routes
    {{ mixpostAssets() }}
    @inertiaHead
</head>
<body class="antialiased font-sans">
@inertia
</body>
</html>
