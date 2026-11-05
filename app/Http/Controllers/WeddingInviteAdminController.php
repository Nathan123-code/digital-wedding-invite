<?php

namespace App\Http\Controllers;

use App\Models\EventSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WeddingInviteAdminController extends Controller
{
    public function login(): View
    {
        return view('admin.admin-login', [
            'eventSettings' => EventSetting::current(),
        ]);
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $configuredPassword = config('event_admin.password');

        if (
            ! is_string($configuredPassword)
            || ! hash_equals((string) config('event_admin.username'), $credentials['username'])
            || ! hash_equals($configuredPassword, $credentials['password'])
        ) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'The admin credentials are incorrect.']);
        }

        $request->session()->regenerate();
        $request->session()->put('admin_authenticated', true);

        return to_route('admin.dashboard');
    }

    public function dashboard(): View
    {
        $disk = Storage::disk('public');
        $photoShareMedia = collect($disk->files('wedding-gallery'))
            ->map(function (string $path) use ($disk): array {
                $mimeType = $disk->mimeType($path) ?: '';

                return [
                    'filename' => basename($path),
                    'url' => $disk->url($path),
                    'type' => str_starts_with($mimeType, 'video/') ? 'video' : 'image',
                ];
            })
            ->values();

        return view('admin.admin-dashboard', [
            'eventSettings' => EventSetting::current(),
            'floorPlanUrl' => $this->assetUrl('floor-plan'),
            'menuUrl' => $this->assetUrl('menu'),
            'photoShareMedia' => $photoShareMedia,
        ]);
    }

    public function updateEventSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bride_name' => ['required', 'string', 'max:255'],
            'groom_name' => ['required', 'string', 'max:255'],
        ]);

        EventSetting::current()->update($validated);

        return back()->with('success', 'Couple names updated.');
    }

    public function upload(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'asset_type' => ['required', 'in:floor-plan,menu'],
        ], [
            'asset.mimes' => 'Only JPG, PNG, and WEBP images are allowed.',
            'asset.max' => 'The image must not be larger than 10 MB.',
        ]);

        $disk = Storage::disk('public');
        foreach ($disk->files('event-assets') as $existingFile) {
            if (pathinfo($existingFile, PATHINFO_FILENAME) === $validated['asset_type']) {
                $disk->delete($existingFile);
            }
        }

        $validated['asset']->storeAs(
            'event-assets',
            $validated['asset_type'].'.'.$validated['asset']->extension(),
            'public',
        );

        return back()->with('success', ucfirst(str_replace('-', ' ', $validated['asset_type'])).' image updated.');
    }

    public function deleteAsset(string $assetType): RedirectResponse
    {
        abort_unless(in_array($assetType, ['floor-plan', 'menu'], true), 404);

        $disk = Storage::disk('public');
        foreach ($disk->files('event-assets') as $existingFile) {
            if (pathinfo($existingFile, PATHINFO_FILENAME) === $assetType) {
                $disk->delete($existingFile);
            }
        }

        return back()->with('success', ucfirst(str_replace('-', ' ', $assetType)).' image deleted.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_authenticated');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('invite.seat');
    }

    private function assetUrl(string $type): ?string
    {
        $path = collect(Storage::disk('public')->files('event-assets'))
            ->first(fn (string $file): bool => pathinfo($file, PATHINFO_FILENAME) === $type);

        return $path ? Storage::disk('public')->url($path) : null;
    }
}
