<?php

namespace App\Http\Controllers\Admin;

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
            'admin.members.index',
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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'position_id' => 'required',
            'team_id' => 'required',
            'employment_type_id' => 'required',

        ]);

        Member::create([

            'eid' => 'EMP' . str_pad(Member::count() + 1, 3, '0', STR_PAD_LEFT),
            'name' => $request->name,
            'position_id' => $request->position_id,
            'team_id' => $request->team_id,
            'employment_type_id' => $request->employment_type_id,
            'join_date' => $request->join_date,
            'end_date' => $request->end_date,

        ]);

        return redirect()
            ->to('/admin/members');
    }

   public function update(Request $request, $id)
{
    $member = Member::findOrFail($id);

    $request->validate([
    
        'name' => 'required',
        'position_id' => 'required',
        'team_id' => 'required',
        'employment_type_id' => 'required',

    ]);

    $member->update([

        'name' => $request->name,
        'position_id' => $request->position_id,
        'team_id' => $request->team_id,
        'employment_type_id' => $request->employment_type_id,
        'join_date' => $request->join_date,
        'end_date' => $request->end_date,

    ]);

    return redirect()
        ->to('/admin/members')
        ->with('success', 'Member berhasil diupdate');
}
    public function destroy($id)
    {
        $member = Member::findOrFail($id);

        $member->delete();

        return redirect()
            ->to('/admin/members')
            ->with('success', 'Member berhasil dihapus');
    }

    public function export()
{
    return Excel::download(
        new MembersExport,
        'Data Members Developer.xlsx'
    );
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    Excel::import(
        new MembersImport,
        $request->file('file')
    );

    return redirect()
        ->to('/admin/members')
        ->with('success', 'Data berhasil diimport');
}

public function downloadTemplate()
{
    return response()->download(
        public_path('templates/member_template.xlsx')
    );
}


}