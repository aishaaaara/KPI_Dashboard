<?php

namespace App\Imports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MembersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithMapping
{
    use SkipsFailures;

    private array $skippedNames = [];

    // ── Helper: ekstrak ID dari string "1 - Nama" atau angka biasa ──
    private function extractId($value): ?int
    {
        if (empty($value)) return null;
        if (is_numeric($value)) return (int) $value;
        if (preg_match('/^(\d+)\s*-/', $value, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    // ── Helper: parse tanggal dari Excel serial atau string ──
    private function parseDate($value): ?string
    {
        if (empty($value)) return null;
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    // ── Mapping: normalisasi row sebelum validasi & model ──
    // Baris kosong (name kosong) di-return dengan semua null
    // supaya tidak memicu validasi error
    public function map($row): array
    {
        if (empty($row['name']) || trim($row['name']) === '') {
            return [
                'name'               => null,
                'position_id'        => null,
                'team_id'            => null,
                'employment_type_id' => null,
                'join_date'          => null,
                'end_date'           => null,
            ];
        }

        return [
            'name'               => trim($row['name']),
            'position_id'        => $row['position_id'] ?? null,
            'team_id'            => $row['team_id'] ?? null,
            'employment_type_id' => $row['employment_type_id'] ?? null,
            'join_date'          => $row['join_date'] ?? null,
            'end_date'           => $row['end_date'] ?? null,
        ];
    }

    // ── Model: proses tiap baris yang lolos validasi ──
    public function model(array $row)
    {
        // Skip baris kosong (name null setelah mapping)
        if (empty($row['name'])) {
            return null;
        }

        // Skip & catat kalau nama sudah terdaftar
        if (Member::where('name', $row['name'])->exists()) {
            $this->skippedNames[] = '"' . $row['name'] . '" (nama sudah terdaftar)';
            return null;
        }

        // Skip & catat kalau data tidak lengkap
        if (
            empty($this->extractId($row['position_id'])) ||
            empty($this->extractId($row['team_id'])) ||
            empty($this->extractId($row['employment_type_id']))
        ) {
            $this->skippedNames[] = '"' . $row['name'] . '" (data tidak lengkap)';
            return null;
        }

        // Generate EID otomatis
        $lastEid    = Member::orderByRaw('CAST(SUBSTRING(eid, 4) AS UNSIGNED) DESC')->value('eid');
        $nextNumber = $lastEid ? (int) substr($lastEid, 3) + 1 : 1;
        $eid        = 'EMP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return new Member([
            'eid'                => $eid,
            'name'               => $row['name'],
            'position_id'        => $this->extractId($row['position_id']),
            'team_id'            => $this->extractId($row['team_id']),
            'employment_type_id' => $this->extractId($row['employment_type_id']),
            'join_date'          => $this->parseDate($row['join_date'] ?? null),
            'end_date'           => $this->parseDate($row['end_date'] ?? null),
        ]);
    }

    // ── Validasi: semua nullable karena baris kosong di-handle di map() ──
    public function rules(): array
    {
        return [
            'name'               => 'nullable|string',
            'position_id'        => 'nullable',
            'team_id'            => 'nullable',
            'employment_type_id' => 'nullable',
            'join_date'          => 'nullable',
            'end_date'           => 'nullable',
        ];
    }

    // ── Getter untuk controller ──
    public function getSkippedNames(): array
    {
        return $this->skippedNames;
    }
}