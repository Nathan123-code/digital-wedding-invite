@extends('layouts.wedding-invite')

@section('content')
    <section class="panel panel-menu">
        @if ($menuUrl)
            <div class="uploaded-event-image uploaded-menu-image">
                <img src="{{ $menuUrl }}" alt="Wedding menu">
            </div>
        @else
            <div class="menu-card">
            <div class="script-name menu-script">{{ $eventSettings->bride_name }} &amp; {{ $eventSettings->groom_name }}</div>
            <div class="menu-header">Menu</div>
            <div class="menu-items">
                <div class="menu-item"><span class="menu-label">Entrée</span><span class="menu-name">Beef Tenderloin</span></div>
                <div class="menu-item"><span class="menu-label">Main</span><span class="menu-name">Roasted Chicken</span></div>
                <div class="menu-item"><span class="menu-label">Dessert</span><span class="menu-name">Chocolate Mousse</span></div>
                <div class="menu-item"><span class="menu-label">Snack</span><span class="menu-name">Mini Canapés</span></div>
            </div>
            </div>
        @endif

    </section>
@endsection
