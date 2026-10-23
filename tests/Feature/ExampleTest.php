<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('an authenticated admin can update the couple names', function () {
    $response = $this->withSession(['admin_authenticated' => true])
        ->post('/admin/event-settings', [
            'bride_name' => 'Maya',
            'groom_name' => 'Raka',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('event_settings', [
        'bride_name' => 'Maya',
        'groom_name' => 'Raka',
    ]);

    $this->get('/menu')
        ->assertOk()
        ->assertSee('Maya')
        ->assertSee('Raka');
});

test('an authenticated admin can delete an event image', function () {
    Storage::fake('public');
    Storage::disk('public')->put('event-assets/menu.png', 'menu image');

    $response = $this->withSession(['admin_authenticated' => true])
        ->delete('/admin/assets/menu');

    $response->assertRedirect();
    Storage::disk('public')->assertMissing('event-assets/menu.png');
});

test('guest seats support named tables', function () {
    $this->post('/admin/guests', [
        'name' => 'Maya',
        'table_name' => 'Table Dahlia',
        'seat_position' => 'B',
    ])->assertRedirect('/admin/login');

    $this->withSession(['admin_authenticated' => true])
        ->post('/admin/guests', [
            'name' => 'Maya',
            'table_name' => 'Table Dahlia',
            'seat_position' => 'B',
        ])
        ->assertRedirect();

    $this->get('/find-your-seat')
        ->assertOk()
        ->assertSee('Table Dahlia')
        ->assertSee('B');
});

test('guest imports accept Indonesian column names', function () {
    $file = UploadedFile::fake()->createWithContent(
        'guests.csv',
        "nama,meja,kursi\nMaya,Table Dahlia,B\n",
    );

    $this->withSession(['admin_authenticated' => true])
        ->post('/admin/guests/import', ['guest_file' => $file])
        ->assertRedirect();

    $this->assertDatabaseHas('guests', [
        'name' => 'Maya',
        'table_name' => 'Table Dahlia',
        'seat_position' => 'B',
    ]);
});

test('guest imports accept rows without headers and uppercase names', function () {
    $file = UploadedFile::fake()->createWithContent(
        'guests.csv',
        "DEWI,Table Fern,B\n",
    );

    $this->withSession(['admin_authenticated' => true])
        ->post('/admin/guests/import', ['guest_file' => $file])
        ->assertRedirect();

    $this->assertDatabaseHas('guests', [
        'name' => 'DEWI',
        'table_name' => 'Table Fern',
        'seat_position' => 'B',
    ]);
});

test('an authenticated admin can delete all guests', function () {
    $this->withSession(['admin_authenticated' => true])
        ->post('/admin/guests', [
            'name' => 'Maya',
            'table_name' => 'Table Dahlia',
            'seat_position' => 'B',
        ]);

    $this->withSession(['admin_authenticated' => true])
        ->delete('/admin/guests')
        ->assertRedirect()
        ->assertSessionHas('success', '1 guest(s) deleted.');

    $this->assertDatabaseCount('guests', 0);
});

test('an authenticated admin can export shared photo media', function () {
    Storage::fake('public');
    Storage::disk('public')->put('wedding-gallery/photo.jpg', 'photo content');

    $response = $this->withSession(['admin_authenticated' => true])
        ->get('/admin/photo-share/export');

    $response->assertOk()
        ->assertDownload('photo-share-export-'.now()->format('Y-m-d-His').'.zip');
});
