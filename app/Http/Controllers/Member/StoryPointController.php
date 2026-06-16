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
    $periods = Period::latest()->get();

    $selectedPeriod = $request->period_id ?? $periods->first()?->id;

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
        'admin.story-points.index',
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
