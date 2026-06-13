<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\Position;
use App\Models\Team;
use App\Models\EmploymentType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MembersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Member([
            'eid' => 'EMP' . str_pad(Member::count() + 1, 3, '0', STR_PAD_LEFT),
            'name' => $row['name'],

            'position_id' => Position::where(
                'name',
                $row['position']
            )->first()?->id,

            'team_id' => Team::where(
                'name',
                $row['team']
            )->first()?->id,

            'employment_type_id' => EmploymentType::where(
                'name',
                $row['employment_type']
            )->first()?->id,

            'join_date' => $row['join_date'],
            'end_date' => $row['end_date'],
        ]);
    }
}