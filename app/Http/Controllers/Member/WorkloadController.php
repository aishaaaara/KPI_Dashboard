<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workload;
use App\Models\Member;
use App\Models\Period;


class WorkloadController extends Controller
{
public function index(Request $request)
{
    $periods = Period::latest()->get();

    $selectedPeriod = $request->period_id ?? $periods->first()?->id;

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
}