<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Position;
use App\Models\Team;
use App\Models\EmploymentType;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::with([
            'position',
            'team',
            'employmentType'
        ])->latest()->get();

        $positions = Position::all();
        $teams = Team::all();
        $employmentTypes = EmploymentType::all();

        return view(
            'admin.members.index',
            compact(
                'members',
                'positions',
                'teams',
                'employmentTypes'
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
            ->route('members.index');
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
        ->route('members.index')
        ->with('success', 'Member berhasil diupdate');
}
    public function destroy($id)
    {
        $member = Member::findOrFail($id);

        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('success', 'Member berhasil dihapus');
    }
}