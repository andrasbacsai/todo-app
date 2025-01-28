<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>{{ $title ?? 'Template' }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('jata.png') }}">

    <style>
        [x-cloak] {
            display: none;
        }
    </style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @auth
        <script type="text/javascript" src="{{ URL::asset('js/echo.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('js/pusher.js') }}"></script>
    @endauth
    <link rel="preconnect" href="https://api.fonts.coollabs.io">
    <link
        href="https://api.fonts.coollabs.io/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Roboto+Mono:wght@484&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    @livewireStyles
    @laravelPWA
</head>

<body>
    <livewire:toasts />
    @auth
        <script data-navigate-once>
            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: "{{ config('reverb.apps.apps.0.key') }}",
                wsHost: "{{ config('reverb.apps.apps.0.options.host') }}",
                wsPort: "{{ config('reverb.apps.apps.0.options.port', 80) }}",
                wssPort: "{{ config('reverb.apps.apps.0.options.port', 443) }}",
                forceTLS: "{{ config('reverb.apps.apps.0.options.scheme', 'https') }}" === 'https',
                enabledTransports: ['ws', 'wss'],
                enableLogging: true,
            });
        </script>
        <div class="mx-auto px-4 sm:px-8 py-2 pt-4">
            @php
                $breadcrumbs = [];
                $path = pathinfo(request()->url())['basename'];
                if (Route::currentRouteName() === 'dashboard') {
                    $breadcrumbs = ['Dashboard', 'General'];
                }
                if ($path === 'projects') {
                    $breadcrumbs = ['Dashboard', 'Projects'];
                }
                if ($path === 'instance-settings') {
                    $breadcrumbs = ['Instance Settings'];
                }
                if ($path === 'billing') {
                    $breadcrumbs = ['Billing'];
                }
            @endphp
            <x-navigation :breadcrumbs="$breadcrumbs" />
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </div>
    @else
        {{ $slot }}
    @endauth
    @livewireScriptConfig
</body>

</html>
