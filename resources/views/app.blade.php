<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth overflow-x-hidden">
<head>
    <title inertia>Mixpost{{ config('app.name') ? ' - ' . config('app.name') : '' }}</title>
    <meta name="robots" content="noindex, nofollow">
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('/vendor/mixpost/favicon.ico') }}">
    @routes
    {{ mixpostAssets() }}
    @inertiaHead
</head>
<body class="font-sans">
@inertia
</body>
</html>
