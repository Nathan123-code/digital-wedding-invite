<?php

namespace App\Imports;

use App\Models\Guest;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class GuestListImport implements ToCollection
{
    public int $importedCount = 0;

    public int $skippedCount = 0;

    public function collection(Collection $rows): void
    {
        $rows = $rows->map(function ($row): array {
            return is_object($row) && method_exists($row, 'toArray')
                ? array_values($row->toArray())
                : array_values((array) $row);
        })->values();
        $headerIndexes = $this->headerIndexes($rows->first() ?? []);

        foreach ($rows as $index => $values) {
            if ($index === 0 && $headerIndexes !== null) {
                continue;
            }

            $name = $this->valueFor($values, $headerIndexes, ['name', 'nama', 'nama_tamu', 'guest_name'], 0);
            $tableName = $this->valueFor($values, $headerIndexes, ['table', 'table_name', 'meja', 'nama_meja'], 1);
            $seatPosition = $this->valueFor($values, $headerIndexes, ['seat', 'seat_position', 'kursi', 'chair', 'nomor_kursi'], 2);

            if ($name === '' || $tableName === '' || $seatPosition === '') {
                $this->skippedCount++;

                continue;
            }

            Guest::updateOrCreate(
                ['name' => $name],
                [
                    'table_name' => $tableName,
                    'seat_position' => $seatPosition,
                    'seat' => $tableName,
                ],
            );
            $this->importedCount++;
        }
    }

    private function headerIndexes(array $values): ?array
    {
        $indexes = [];

        foreach ($values as $index => $value) {
            $header = $this->normalizeHeader((string) $value);

            if (in_array($header, ['name', 'nama', 'nama_tamu', 'guest_name'], true)) {
                $indexes['name'] = $index;
            }

            if (in_array($header, ['table', 'table_name', 'meja', 'nama_meja'], true)) {
                $indexes['table'] = $index;
            }

            if (in_array($header, ['seat', 'seat_position', 'kursi', 'chair', 'nomor_kursi'], true)) {
                $indexes['seat'] = $index;
            }
        }

        return isset($indexes['name'], $indexes['table'], $indexes['seat']) ? $indexes : null;
    }

    private function valueFor(array $values, ?array $headerIndexes, array $keys, int $fallbackIndex): string
    {
        if ($headerIndexes !== null) {
            $key = match ($fallbackIndex) {
                0 => 'name',
                1 => 'table',
                default => 'seat',
            };

            return trim((string) ($values[$headerIndexes[$key]] ?? ''));
        }

        return trim((string) ($values[$fallbackIndex] ?? ''));
    }

    private function normalizeHeader(string $header): string
    {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $header) ?? ''));
    }
}
