<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Login - Wedding Invite</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <main class="admin-page">
            <form class="admin-card" action="{{ route('admin.authenticate') }}" method="POST">
                @csrf
                <div class="script-name">{{ $eventSettings->bride_name }} &amp; {{ $eventSettings->groom_name }}</div>
                <h1>Event Admin</h1>
                <p>Sign in to update the Floor Plan and Menu images.</p>

                @error('username')
                    <div class="admin-error">{{ $message }}</div>
                @enderror

                <label for="username">Username</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus>

                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>

                <button class="upload-submit" type="submit">Sign In</button>
            </form>
        </main>
    </body>
</html>
