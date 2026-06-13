<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Position;
use App\Models\Team;
use App\Models\EmploymentType;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MembersExport;
use App\Imports\MembersImport;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $selectedTeam = $request->team_id;
        $selectedType = $request->employment_type_id;
        $search = $request->search;

        $members = Member::with([
                'position',
                'team',
                'employmentType'
            ])
    ->when($search, function ($query) use ($search) {

        $query->where(function($q) use ($search){

            $q->where('name', 'like', "%{$search}%")
              ->orWhere('eid', 'like', "%{$search}%");

        });

    })
    ->when($selectedTeam, function ($query) use ($selectedTeam) {

        $query->where('team_id', $selectedTeam);

    })
    ->when($selectedType, function ($query) use ($selectedType) {

        $query->where(
            'employment_type_id',
            $selectedType
        );

    })
    ->latest()
    ->get();

        $positions = Position::all();
        $teams = Team::all();
        $employmentTypes = EmploymentType::all();

        return view(
            'member.members.index',
            compact(
                'members',
                'positions',
                'teams',
                'employmentTypes',
                'selectedTeam',
                'selectedType'
            )
        );
    }


    public function export()
{
    return Excel::download(
        new MembersExport,
        'Data Members Developer.xlsx'
    );
}


}