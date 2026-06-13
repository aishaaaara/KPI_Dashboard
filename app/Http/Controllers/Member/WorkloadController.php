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