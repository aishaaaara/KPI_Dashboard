<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workload;
use App\Models\Member;
use App\Models\Period;
use App\Services\NotificationService;

class WorkloadController extends Controller
{
 
public function index(Request $request)
{
     $periods = Period::selectRaw("*, 
        FIELD(month, 'January','February','March','April','May','June',
        'July','August','September','October','November','December') as month_order")
        ->orderBy('year', 'asc')
        ->orderBy('month_order', 'asc')
        ->get();

    $selectedPeriod = $request->period_id ?? $periods->last()?->id;

    $workloads = Workload::with(['member.position', 'period'])
        ->when($selectedPeriod, function ($query) use ($selectedPeriod) {
            $query->where('period_id', $selectedPeriod);
        })
        ->get();

    $existingPeriods = Period::select( 'month','year')->get();

    $workloadCounts = Workload::selectRaw('period_id, count(*) as total')
    ->groupBy('period_id')
    ->pluck('total', 'period_id'); // hasil: [period_id => total]

    $members = Member::with('position')->get();

    return view(
        'admin.workload.index',
        compact(
            'workloads',
            'workloadCounts',
            'members',
            'periods',
            'selectedPeriod',
            'existingPeriods'
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

public function update( Request $request, $id)
{
    $workload = Workload::findOrFail($id);
    $period   = Period::findOrFail($workload->period_id);

    if ($this->isPeriodLocked($period)) {
        return back()->with('error', 'Periode ini sudah terkunci dan tidak dapat diubah.');
    }
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
        'month' => 'required',
        'year'  => 'required|integer',
    ]);

    $selectedDate = \Carbon\Carbon::createFromDate(
        $request->year,
        date('n', strtotime($request->month)),
        1
    )->startOfMonth();

    if ($selectedDate->lt(now()->startOfMonth())) {
        return redirect()->back()->with('error', 'Cannot add a past period');
    }

    $exists = Period::where('month', $request->month)
        ->where('year', $request->year)
        ->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'Period already exists');
    }

    $period  = Period::create([
        'month' => $request->month,
        'year'  => $request->year,
    ]);

    $members = Member::all();

    foreach ($members as $member) {

        // Communication
        \App\Models\Communication::firstOrCreate(
            ['member_id' => $member->id, 'period_id' => $period->id],
            ['clarity' => 0, 'responsiveness' => 0, 'collaboration' => 0, 'overall_score' => 0, 'notes' => null]
        );

        // Story Point
        \App\Models\StoryPoint::firstOrCreate(
            ['member_id' => $member->id, 'period_id' => $period->id],
            ['target' => 0, 'totals' => 0, 'summary' => 0]
        );

        // Workload
        \App\Models\Workload::firstOrCreate(
            ['member_id' => $member->id, 'period_id' => $period->id],
            ['all_task' => 0, 'todo' => 0, 'progress' => 0, 'review' => 0, 'done' => 0]
        );
    }

    app(NotificationService::class)->notifyNewWorkloadPeriod(
        $period->month . ' ' . $period->year
    );

    return redirect()->back()->with('success', 'New period added successfully');
}

private function isPeriodLocked(Period $period): bool
{
    $periodDate = \Carbon\Carbon::createFromDate(
        $period->year,
        date('n', strtotime($period->month)),
        1
    );

    // Terkunci kalau bulannya sudah lewat dari bulan sekarang
    return $periodDate->lt(now()->startOfMonth());
}
}