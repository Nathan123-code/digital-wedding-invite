<?php

use App\Http\Controllers\GuestManagementController;
use App\Http\Controllers\PhotoShareController;
use App\Http\Controllers\WeddingInviteAdminController;
use App\Http\Controllers\WeddingInviteContentController;
use App\Http\Middleware\RequireAdminAuthentication;
use App\Models\EventSetting;
use App\Models\Guest;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('invite.guest-seat-search', [
        'active' => 'seat',
        'eventSettings' => EventSetting::current(),
        'guests' => Guest::query()->orderBy('name')->get(['name', 'table_name', 'seat_position', 'seat']),
    ]);
});

Route::get('/find-your-seat', function () {
    return view('invite.guest-seat-search', [
        'active' => 'seat',
        'eventSettings' => EventSetting::current(),
        'guests' => Guest::query()->orderBy('name')->get(['name', 'table_name', 'seat_position', 'seat']),
    ]);
})->name('invite.seat');

Route::get('/floor-plan', [WeddingInviteContentController::class, 'floorPlan'])->name('invite.floor-plan');

Route::get('/menu', [WeddingInviteContentController::class, 'menu'])->name('invite.menu');

Route::get('/photo-share', [PhotoShareController::class, 'index'])
    ->name('invite.gallery');

Route::post('/photo-share', [PhotoShareController::class, 'store'])
    ->name('invite.gallery.store');

Route::get('/admin/login', [WeddingInviteAdminController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [WeddingInviteAdminController::class, 'authenticate'])->name('admin.authenticate');

Route::middleware(RequireAdminAuthentication::class)->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [WeddingInviteAdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/event-settings', [WeddingInviteAdminController::class, 'updateEventSettings'])->name('event-settings.update');
    Route::post('/upload', [WeddingInviteAdminController::class, 'upload'])->name('upload');
    Route::delete('/assets/{assetType}', [WeddingInviteAdminController::class, 'deleteAsset'])->name('assets.destroy');
    Route::get('/photo-share/export', [PhotoShareController::class, 'export'])->name('photo-share.export');
    Route::post('/logout', [WeddingInviteAdminController::class, 'logout'])->name('logout');
    Route::get('/guests', [GuestManagementController::class, 'index'])->name('guests.index');
    Route::post('/guests', [GuestManagementController::class, 'store'])->name('guests.store');
    Route::post('/guests/import', [GuestManagementController::class, 'import'])->name('guests.import');
    Route::delete('/guests', [GuestManagementController::class, 'destroyAll'])->name('guests.destroy-all');
    Route::put('/guests/{guest}', [GuestManagementController::class, 'update'])->name('guests.update');
    Route::delete('/guests/{guest}', [GuestManagementController::class, 'destroy'])->name('guests.destroy');
});
