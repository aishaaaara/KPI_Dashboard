<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoryPoint;
use App\Models\Member;
use App\Models\Period;


class StoryPointController extends Controller
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

    $storyPoints = StoryPoint::with(['member.position', 'period'])
        ->when($selectedPeriod, function ($query) use ($selectedPeriod) {
            $query->where('period_id', $selectedPeriod);
        })
        ->get();

    $existingPeriods = Period::select( 'month','year')->get();

     $storyPointCounts = StoryPoint::selectRaw('period_id, count(*) as total')
    ->groupBy('period_id')
    ->pluck('total', 'period_id'); // hasil: [period_id => total]

    $members = Member::all();

    return view(
        'member.story-points.index',
        compact(
            'storyPoints',
            'storyPointCounts',
            'members',
            'periods',
            'selectedPeriod',
            'existingPeriods'
        )
    );
}

}
