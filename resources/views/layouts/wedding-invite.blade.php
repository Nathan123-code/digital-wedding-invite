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
    <body style="background-image: url('{{ asset('images/wedding-background.png') }}');">
        <div
            class="opening-screen"
            data-opening-screen
            style="background-image: linear-gradient(rgba(18, 30, 26, 0.18), rgba(18, 30, 26, 0.18)), url('{{ asset('images/wedding-background.png') }}');"
        >
            <div class="opening-card">
                <p class="opening-kicker">Welcome to the wedding of</p>
                <div class="opening-names">
                    {{ $eventSettings->bride_name ?? 'Wedding' }} &amp; {{ $eventSettings->groom_name ?? 'Invite' }}
                </div>
                <button class="opening-button" type="button" data-open-invitation>
                    Open Invitation
                </button>
            </div>
        </div>

        <div class="page-shell">
            @include('partials.wedding-invite-navigation')

            <main class="invite-sections">
                @yield('content')
            </main>
        </div>
    </body>
</html>
