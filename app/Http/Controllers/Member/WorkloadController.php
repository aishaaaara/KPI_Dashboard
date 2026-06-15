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
        'member.workload.index',
        compact(
            'workloads',
            'members',
            'periods',
            'selectedPeriod'
        )
    );
}

}