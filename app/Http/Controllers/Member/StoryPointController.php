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

    $storyPoints = StoryPoint::with([
        'member.position',
        'period'
    ])

    ->when($selectedPeriod,function($query) use ($selectedPeriod){

        $query->where(
            'period_id',
            $selectedPeriod
        );

    })

    ->get();

    $members = Member::all();

    return view(
        'member.story-points.index',
        compact(
            'storyPoints',
            'members',
            'periods',
            'selectedPeriod'
        )
    );
}

}
