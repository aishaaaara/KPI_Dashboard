<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\Position;
use App\Models\Team;
use App\Models\EmploymentType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MembersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    private function extractId($value): ?int
    {
        if (empty($value)) return null;
        if (is_numeric($value)) return (int) $value;
        if (preg_match('/^(\d+)\s*-/', $value, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

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

    public function model(array $row)
{
    if (empty($row['name'])) {
        return null;
    }

    // Ambil nomor EID tertinggi yang sudah ada
    $lastEid = \App\Models\Member::orderByRaw('CAST(SUBSTRING(eid, 4) AS UNSIGNED) DESC')
        ->value('eid');

    $nextNumber = $lastEid
        ? (int) substr($lastEid, 3) + 1
        : 1;

    $eid = 'EMP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

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

    public function rules(): array
    {
        return [
            'name'               => 'required|string',
            'position_id'        => 'required',
            'team_id'            => 'required',
            'employment_type_id' => 'required',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name.required'               => 'Kolom name tidak boleh kosong',
            'position_id.required'        => 'Kolom position_id tidak boleh kosong',
            'team_id.required'            => 'Kolom team_id tidak boleh kosong',
            'employment_type_id.required' => 'Kolom employment_type_id tidak boleh kosong',
        ];
    }
}