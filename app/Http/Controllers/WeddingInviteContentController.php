<?php

namespace App\Http\Controllers;

use App\Models\EventSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WeddingInviteContentController extends Controller
{
    public function floorPlan(): View
    {
        return view('invite.floor-plan', [
            'active' => 'floor-plan',
            'eventSettings' => EventSetting::current(),
            'floorPlanUrl' => $this->assetUrl('floor-plan'),
        ]);
    }

    public function menu(): View
    {
        return view('invite.menu', [
            'active' => 'menu',
            'eventSettings' => EventSetting::current(),
            'menuUrl' => $this->assetUrl('menu'),
        ]);
    }

    private function assetUrl(string $type): ?string
    {
        $path = collect(Storage::disk('public')->files('event-assets'))
            ->first(fn (string $file): bool => pathinfo($file, PATHINFO_FILENAME) === $type);

        return $path ? Storage::disk('public')->url($path) : null;
    }
}
