<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MembersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Member::with([
            'position',
            'team',
            'employmentType'
        ])->get()->map(function ($member) {
            return [
                'eid' => $member->eid,
                'name' => $member->name,
                'position' => $member->position->name,
                'team' => $member->team->name,
                'employment_type' => $member->employmentType->name,
                'join_date' => $member->join_date,
                'end_date' => $member->end_date,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'EID',
            'Name',
            'Position',
            'Team',
            'Employment Type',
            'Join Date',
            'End Date'
        ];
    }
}