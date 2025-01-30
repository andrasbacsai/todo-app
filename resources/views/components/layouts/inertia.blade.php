<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @routes
    @vite('resources/js/inertia.js')
    @auth
        <script type="text/javascript" src="{{ URL::asset('js/echo.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('js/pusher.js') }}"></script>
    @endauth
    @inertiaHead
</head>

<body>
    <script>
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
    @inertia
</body>

</html>
