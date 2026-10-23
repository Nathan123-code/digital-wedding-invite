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
                        <a class="admin-link" href="{{ route('admin.guests.index') }}">Manage Guests</a>
                        <a class="admin-link" href="{{ route('invite.seat') }}">Back to Guest Website</a>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button class="admin-link" type="submit">Sign Out</button>
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
                    <h2>Photo Share Backup</h2>
                    <p>Download all guest photos and videos as a ZIP file. The ZIP also includes a manifest with the file details.</p>
                    <a class="admin-link" href="{{ route('admin.photo-share.export') }}">Download Photo Share ZIP</a>
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
                                    <button class="admin-link danger-link" type="submit">Delete Current {{ $label }}</button>
                                </form>
                            @endif
                        </section>
                    @endforeach
                </div>
            </div>
        </main>
    </body>
</html>
