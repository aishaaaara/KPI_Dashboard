<?php

namespace App\Imports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MembersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Handle tanggal — bisa berupa angka serial Excel atau string
        $joinDate = null;
        $endDate  = null;

        if (!empty($row['join_date'])) {
            $joinDate = is_numeric($row['join_date'])
                ? Date::excelToDateTimeObject($row['join_date'])->format('Y-m-d')
                : \Carbon\Carbon::parse($row['join_date'])->format('Y-m-d');
        }

        if (!empty($row['end_date'])) {
            $endDate = is_numeric($row['end_date'])
                ? Date::excelToDateTimeObject($row['end_date'])->format('Y-m-d')
                : \Carbon\Carbon::parse($row['end_date'])->format('Y-m-d');
        }

        return new Member([
            'name'               => $row['name'],
            'position_id'        => $row['position_id'],
            'team_id'            => $row['team_id'],
            'employment_type_id' => $row['employment_type_id'],
            'join_date'          => $joinDate,
            'end_date'           => $endDate,
        ]);
    }
}