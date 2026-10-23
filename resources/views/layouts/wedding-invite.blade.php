<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ ($eventSettings->bride_name ?? 'Wedding') . ' & ' . ($eventSettings->groom_name ?? 'Invite') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Manrope:wght@400;500;600;700&family=Parisienne&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="page-shell">
            <a class="admin-login-link" href="{{ route('admin.login') }}">Admin Login</a>

            <button class="theme-toggle" aria-label="Toggle theme">
                <span>◐</span>
            </button>

            @include('partials.wedding-invite-navigation')

            <main class="invite-sections">
                @yield('content')
            </main>
        </div>
    </body>
</html>
