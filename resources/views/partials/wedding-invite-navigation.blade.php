<nav class="bottom-nav top-nav" aria-label="Section navigation">
    <a class="{{ $active === 'seat' ? 'active' : '' }}" href="{{ route('invite.seat') }}">Find Your Seat</a>
    <a class="{{ $active === 'floor-plan' ? 'active' : '' }}" href="{{ route('invite.floor-plan') }}">Floor Plan</a>
    <a class="{{ $active === 'menu' ? 'active' : '' }}" href="{{ route('invite.menu') }}">Menu</a>
    <a class="{{ $active === 'gallery' ? 'active' : '' }}" href="{{ route('invite.gallery') }}">Photo Share</a>
</nav>
