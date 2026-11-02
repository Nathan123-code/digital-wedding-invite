@extends('layouts.wedding-invite')

@section('content')
    <section class="panel panel-hero">
        <div class="photo-overlay" aria-hidden="true"></div>
        <div class="hero-card">
            <div class="script-name">{{ $eventSettings->bride_name }} &amp; {{ $eventSettings->groom_name }}</div>
            <h1>Please Find Your Seat</h1>

            <div class="seat-search">
                <label for="guest-search">Search your name to find your table</label>
                <input
                    id="guest-search"
                    class="guest-search-input"
                    type="search"
                    placeholder="Type your name..."
                    autocomplete="off"
                    spellcheck="false"
                    data-guests='@json($guests->map(fn ($guest) => ["name" => $guest->name, "table" => $guest->table_name ?: $guest->seat, "seat" => $guest->seat_position])->values())'
                    data-guest-search
                >
                <div class="search-results" data-search-results aria-live="polite"></div>
            </div>

            <div class="wedding-hashtag">
                <p>Capture your special moments and share them with</p>
                <a href="#theWillofJulia" data-copy-hashtag="#theWillofJulia">
                    <strong>#theWillofJulia</strong>
                </a>
            </div>
        </div>

    </section>
@endsection
