<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Guest Management - Wedding Invite</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <main class="admin-page">
            <div class="admin-card admin-dashboard">
                <div class="admin-heading">
                    <div>
                        <div class="script-name">{{ $eventSettings->bride_name }} &amp; {{ $eventSettings->groom_name }}</div>
                        <h1>Guest Management</h1>
                    </div>
                    <div class="admin-actions">
                        <a class="admin-link" href="{{ route('admin.dashboard') }}">Back to Dashboard</a>
                        <a class="admin-link" href="{{ route('invite.seat') }}">Guest Website</a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="admin-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="admin-error">{{ $errors->first() }}</div>
                @endif

                <section class="guest-form-section">
                    <h2>Add Guest</h2>
                    <form class="guest-admin-form" action="{{ route('admin.guests.store') }}" method="POST">
                        @csrf
                        <input name="name" type="text" placeholder="Guest name" required>
                        <input name="table_name" type="text" placeholder="Table name (e.g. Table Fern)" required>
                        <input name="seat_position" type="text" placeholder="Seat (e.g. B)" required>
                        <button class="upload-submit" type="submit">Add Guest</button>
                    </form>
                </section>

                <section class="guest-form-section">
                    <h2>Import CSV or Excel</h2>
                    <p>You may include headers such as <strong>name</strong>, <strong>table</strong>, and <strong>seat</strong>, or upload rows without headers. Indonesian headers <strong>nama</strong>, <strong>meja</strong>, and <strong>kursi</strong> are also supported. Without headers, use one row per guest in this order: <strong>Name, Table, Seat</strong>. Example: Dewi, Table Fern, B.</p>
                    <form class="guest-admin-form" action="{{ route('admin.guests.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input name="guest_file" type="file" accept=".csv,.txt,.xlsx,.xls" required>
                        <button class="upload-submit" type="submit">Import Guest List</button>
                    </form>
                </section>

                <section class="guest-form-section guest-danger-section">
                    <h2>Reset Guest List</h2>
                    <p>Delete all guest names and seat assignments to prepare for another event. This does not delete event images or couple names.</p>
                    <form action="{{ route('admin.guests.destroy-all') }}" method="POST" onsubmit="return confirm('Delete all guests and seat assignments? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button class="admin-link danger-link" type="submit">Delete All Guests</button>
                    </form>
                </section>

                <div class="guest-table-wrap">
                    <table class="guest-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Table</th>
                                <th>Seat</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($guests as $guest)
                                <form id="guest-update-{{ $guest->id }}" action="{{ route('admin.guests.update', $guest) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                </form>
                                <form id="guest-delete-{{ $guest->id }}" action="{{ route('admin.guests.destroy', $guest) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <tr>
                                    <td><input form="guest-update-{{ $guest->id }}" name="name" type="text" value="{{ $guest->name }}" required></td>
                                    <td><input form="guest-update-{{ $guest->id }}" name="table_name" type="text" value="{{ $guest->table_name ?: $guest->seat }}" required></td>
                                    <td><input form="guest-update-{{ $guest->id }}" name="seat_position" type="text" value="{{ $guest->seat_position }}" required></td>
                                    <td class="guest-actions">
                                        <button class="admin-link" form="guest-update-{{ $guest->id }}" type="submit">Save</button>
                                        <button class="admin-link danger-link" form="guest-delete-{{ $guest->id }}" type="submit">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4">No guests added yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </body>
</html>
