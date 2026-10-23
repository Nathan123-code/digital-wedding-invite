<?php

namespace App\Http\Controllers;

use App\Imports\GuestListImport;
use App\Models\EventSetting;
use App\Models\Guest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class GuestManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.guest-management', [
            'eventSettings' => EventSetting::current(),
            'guests' => Guest::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['seat'] = $data['table_name'];
        Guest::create($data);

        return back()->with('success', 'Guest added.');
    }

    public function update(Request $request, Guest $guest): RedirectResponse
    {
        $data = $this->validated($request);
        $data['seat'] = $data['table_name'];
        $guest->update($data);

        return back()->with('success', 'Guest updated.');
    }

    public function destroy(Guest $guest): RedirectResponse
    {
        $guest->delete();

        return back()->with('success', 'Guest deleted.');
    }

    public function destroyAll(): RedirectResponse
    {
        $deletedCount = Guest::query()->delete();

        return back()->with('success', "{$deletedCount} guest(s) deleted.");
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'guest_file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'],
        ], [
            'guest_file.mimes' => 'Please upload a CSV or Excel file.',
            'guest_file.max' => 'The import file must not be larger than 10 MB.',
        ]);

        $import = new GuestListImport;
        Excel::import($import, $request->file('guest_file'));

        if ($import->importedCount === 0) {
            return back()
                ->withInput()
                ->withErrors([
                    'guest_file' => 'No valid guest rows were found. Use the first row for Name/Nama, Table/Meja, and Seat/Kursi headers.',
                ]);
        }

        $message = "{$import->importedCount} guest(s) imported or updated.";

        if ($import->skippedCount > 0) {
            $message .= " {$import->skippedCount} incomplete row(s) skipped.";
        }

        return back()->with('success', $message);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'table_name' => ['required', 'string', 'max:100'],
            'seat_position' => ['required', 'string', 'max:50'],
        ]);
    }
}
