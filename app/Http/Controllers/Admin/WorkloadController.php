<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workload;
use App\Models\Member;
use App\Models\Period;


class WorkloadController extends Controller
{
    public function index(Request $request)
{
    $selectedPeriod = $request->period_id;

    $periods = Period::latest()->get();

    $workloads = Workload::with([
        'member.position',
        'period'
    ]);

    if($selectedPeriod){
        $workloads->where(
            'period_id',
            $selectedPeriod
        );
    }

    $workloads = $workloads->get();

    $members = Member::with('position')->get();

    return view(
        'admin.workload.index',
        compact(
            'workloads',
            'members',
            'periods',
            'selectedPeriod'
        )
    );
}

public function store(Request $request)
{
    $request->validate([
        'member_id' => 'required',
        'period_id' => 'required',
        'all_task' => 'required|integer|min:0',
        'todo' => 'required|integer|min:0',
        'progress' => 'required|integer|min:0',
        'review' => 'required|integer|min:0',
        'done' => 'required|integer|min:0',
    ]);

    Workload::create([
        'member_id' => $request->member_id,
        'period_id' => $request->period_id,
        'all_task' => $request->all_task,
        'todo' => $request->todo,
        'progress' => $request->progress,
        'review' => $request->review,
        'done' => $request->done,
    ]);

    return redirect()
        ->back()
        ->with(
            'success',
            'Workload added successfully'
        );
}

public function update(
    Request $request,
    $id
)
{
    $workload = Workload::findOrFail($id);

    $workload->update([
        'all_task' => $request->all_task,
        'todo' => $request->todo,
        'progress' => $request->progress,
        'review' => $request->review,
        'done' => $request->done,
    ]);

    return redirect()
        ->back()
        ->with(
            'success',
            'Workload updated successfully'
        );
}

public function destroy($id)
{
    Workload::findOrFail($id)
        ->delete();

    return redirect()
        ->back()
        ->with(
            'success',
            'Workload deleted successfully'
        );
}

public function destroyPeriod($id)
{
    Workload::where('period_id', $id)
        ->delete();

    return redirect()
        ->back()
        ->with(
            'success',
            'Workload for the period deleted successfully'
        );


}

public function storePeriod(Request $request)
{
    $request->validate([
        'period_id' => 'required',
    ]);

    $periodId = $request->period_id;

    $members = Member::all();

    foreach ($members as $member) {
        Workload::create([
            'member_id' => $member->id,
            'period_id' => $periodId,
            'all_task' => 0,
            'todo' => 0,
            'progress' => 0,
            'review' => 0,
            'done' => 0,
        ]);
    }

    return redirect()
        ->back()
        ->with(
            'success',
            'Workload for the period added successfully'
        );

}

}