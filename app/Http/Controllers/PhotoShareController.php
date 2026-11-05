<?php

namespace App\Http\Controllers;

use App\Models\EventSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PhotoShareController extends Controller
{
    public function index(): View
    {
        $media = collect(Storage::disk('public')->files('wedding-gallery'))
            ->map(function (string $path): array {
                $mimeType = Storage::disk('public')->mimeType($path) ?: '';

                return [
                    'url' => Storage::disk('public')->url($path),
                    'type' => str_starts_with($mimeType, 'video/') ? 'video' : 'image',
                ];
            })
            ->values();

        return view('invite.photo-share', [
            'active' => 'gallery',
            'eventSettings' => EventSetting::current(),
            'media' => $media,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'media' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,gif,webp,mp4,mov,webm,avi',
                'max:20480',
            ],
        ], [
            'media.required' => 'Please choose a photo or video first.',
            'media.mimes' => 'Only image and video files are allowed.',
            'media.max' => 'The file must not be larger than 20 MB.',
        ]);

        $validated['media']->store('wedding-gallery', 'public');

        return to_route('invite.gallery')->with('success', 'Your photo or video has been shared.');
    }

    public function destroyAll(): RedirectResponse
    {
        $disk = Storage::disk('public');
        $mediaFiles = $disk->allFiles('wedding-gallery');

        if ($mediaFiles === []) {
            return back()->with('success', 'Photo Share is already empty.');
        }

        $disk->delete($mediaFiles);

        return back()->with('success', count($mediaFiles).' Photo Share file(s) deleted.');
    }

    public function destroy(string $filename): RedirectResponse
    {
        $disk = Storage::disk('public');
        $mediaPath = collect($disk->files('wedding-gallery'))
            ->first(fn (string $path): bool => basename($path) === $filename);

        abort_if($mediaPath === null, 404);

        $disk->delete($mediaPath);

        return back()->with('success', 'Photo Share file deleted.');
    }

    public function export(): BinaryFileResponse|RedirectResponse
    {
        $disk = Storage::disk('public');
        $mediaFiles = $disk->files('wedding-gallery');

        if ($mediaFiles === []) {
            return back()->withErrors([
                'photo-share-export' => 'There are no shared photos or videos to export.',
            ]);
        }

        $archivePath = tempnam(storage_path('app'), 'photo-share-export-');

        if ($archivePath === false) {
            return back()->withErrors([
                'photo-share-export' => 'The Photo Share export could not be prepared.',
            ]);
        }

        $archive = new \ZipArchive;
        $archiveStatus = $archive->open($archivePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        if ($archiveStatus !== true) {
            @unlink($archivePath);

            return back()->withErrors([
                'photo-share-export' => 'The Photo Share ZIP file could not be created.',
            ]);
        }

        $manifest = "filename,type,size_bytes,last_modified\n";

        foreach ($mediaFiles as $path) {
            $absolutePath = $disk->path($path);

            if (! is_file($absolutePath)) {
                continue;
            }

            $filename = basename($path);
            $mimeType = $disk->mimeType($path) ?: 'application/octet-stream';
            $type = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
            $size = $disk->size($path);
            $lastModified = date('c', $disk->lastModified($path));

            $archive->addFile($absolutePath, 'media/'.$filename);
            $manifest .= implode(',', [
                $this->csvValue($filename),
                $this->csvValue($type),
                $size,
                $this->csvValue($lastModified),
            ])."\n";
        }

        $archive->addFromString('photo-share-manifest.csv', $manifest);
        $archive->close();

        return response()
            ->download($archivePath, 'photo-share-export-'.now()->format('Y-m-d-His').'.zip')
            ->deleteFileAfterSend(true);
    }

    private function csvValue(string $value): string
    {
        return '"'.str_replace('"', '""', $value).'"';
    }
}
