<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Event Admin - Wedding Invite</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <main class="admin-page">
            <div class="admin-card admin-dashboard">
                <div class="admin-heading">
                    <div>
                        <div class="script-name">{{ $eventSettings->bride_name }} &amp; {{ $eventSettings->groom_name }}</div>
                        <h1>Event Admin</h1>
                    </div>
                    <div class="admin-actions">
                        <a class="admin-button" href="{{ route('admin.guests.index') }}">Manage Guests</a>
                        <a class="admin-button admin-button-secondary" href="{{ route('invite.seat') }}">Guest Website</a>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button class="admin-button admin-button-secondary" type="submit">Sign Out</button>
                        </form>
                    </div>
                </div>

                @if (session('success'))
                    <div class="admin-success">{{ session('success') }}</div>
                @endif

                @error('asset')
                    <div class="admin-error">{{ $message }}</div>
                @enderror

                @if ($errors->has('bride_name') || $errors->has('groom_name'))
                    <div class="admin-error">{{ $errors->first('bride_name') ?: $errors->first('groom_name') }}</div>
                @endif

                @if ($errors->has('photo-share-export'))
                    <div class="admin-error">{{ $errors->first('photo-share-export') }}</div>
                @endif

                <p>Upload one active image for each section. A new upload replaces the previous image.</p>

                <section class="admin-asset admin-event-settings">
                    <div class="admin-section-heading">
                        <div>
                            <h2>Photo Share Backup</h2>
                            <p>Manage guest photos and videos, download a backup, or remove media for the next event.</p>
                        </div>
                        <span class="admin-section-count">{{ $photoShareMedia->count() }} file(s)</span>
                    </div>

                    <div class="admin-photo-share-actions">
                        <a class="admin-button" href="{{ route('admin.photo-share.export') }}">Download ZIP Backup</a>
                        <form action="{{ route('admin.photo-share.destroy-all') }}" method="POST" onsubmit="return confirm('Delete all shared photos and videos? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button class="admin-button admin-button-danger" type="submit">Delete All Media</button>
                        </form>
                    </div>

                    @if ($photoShareMedia->isNotEmpty())
                        <div class="admin-photo-share-grid">
                            @foreach ($photoShareMedia as $item)
                                <div class="admin-photo-share-item">
                                    @if ($item['type'] === 'video')
                                        <video controls preload="metadata" aria-label="Shared wedding video">
                                            <source src="{{ $item['url'] }}">
                                        </video>
                                    @else
                                        <img src="{{ $item['url'] }}" alt="Shared wedding photo">
                                    @endif
                                    <form action="{{ route('admin.photo-share.destroy', $item['filename']) }}" method="POST" onsubmit="return confirm('Delete this Photo Share file? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-button admin-button-danger" type="submit">Delete This File</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="admin-photo-share-empty">
                            <strong>No shared media yet</strong>
                            <span>Uploaded guest photos and videos will appear here.</span>
                        </div>
                    @endif
                </section>

                <section class="admin-asset admin-event-settings">
                    <h2>Couple Names</h2>
                    <form class="guest-admin-form" action="{{ route('admin.event-settings.update') }}" method="POST">
                        @csrf
                        <input name="bride_name" type="text" value="{{ $eventSettings->bride_name }}" placeholder="Bride name" required>
                        <input name="groom_name" type="text" value="{{ $eventSettings->groom_name }}" placeholder="Groom name" required>
                        <button class="upload-submit" type="submit">Save Couple Names</button>
                    </form>
                </section>

                <div class="admin-assets">
                    @foreach (['floor-plan' => 'Floor Plan', 'menu' => 'Menu'] as $type => $label)
                        <section class="admin-asset">
                            <h2>{{ $label }}</h2>
                            @if (($type === 'floor-plan' ? $floorPlanUrl : $menuUrl))
                                <img src="{{ $type === 'floor-plan' ? $floorPlanUrl : $menuUrl }}" alt="Current {{ $label }} image">
                            @else
                                <div class="admin-placeholder">No image uploaded yet.</div>
                            @endif

                            <div class="admin-asset-actions">
                                <form action="{{ route('admin.upload') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input name="asset_type" type="hidden" value="{{ $type }}">
                                    <input name="asset" type="file" accept="image/jpeg,image/png,image/webp" required>
                                    <button class="upload-submit" type="submit">Upload {{ $label }}</button>
                                </form>

                                @if (($type === 'floor-plan' ? $floorPlanUrl : $menuUrl))
                                    <form action="{{ route('admin.assets.destroy', $type) }}" method="POST" onsubmit="return confirm('Delete the current {{ $label }} image?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-button admin-button-danger" type="submit">Delete Current {{ $label }}</button>
                                    </form>
                                @endif
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        </main>
    </body>
</html>
