<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoryPoint;
use App\Models\Member;
use App\Models\Period;


class StoryPointController extends Controller
{
    public function index(Request $request)
{
    $selectedPeriod = $request->period_id;
    $periods = Period::latest()->get();
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
        'admin.story-points.index',
        compact(
            'storyPoints',
            'members',
            'periods',
            'selectedPeriod'
        )
    );
}

public function store(Request $request)
{
    $achievement = $request->target > 0
        ? round(($request->totals / $request->target) * 100)
        : 0;

    StoryPoint::create([
        'member_id' => $request->member_id,
        'period_id' => $request->period_id,
        'target' => $request->target,
        'totals' => $request->totals,
        'summary' => $achievement . '%',
    ]);

    return redirect()
        ->back()
        ->with(
            'success',
            'Story Point berhasil ditambahkan'
        );
}

public function storePeriod(Request $request)
    {
        $exists = Period::where(

                'month',
                $request->month

            )

            ->where(

                'year',
                $request->year

            )

            ->exists();

        if(!$exists){

            Period::create([

                'month' => $request->month,

                'year' => $request->year

            ]);
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'New month added successfully'
            );
    }
    
public function update(Request $request,$id)
{
    $storyPoint =
        StoryPoint::findOrFail($id);

    $achievement = $request->target > 0
        ? round(($request->totals / $request->target) * 100)
        : 0;

    $storyPoint->update([
        'target' => $request->target,
        'totals' => $request->totals,
        'summary' => $achievement,
    ]);

    return redirect()
        ->back()
        ->with(
            'success',
            'Story Point berhasil diupdate'
        );
}

public function destroy($id)
{
    StoryPoint::findOrFail($id)
        ->delete();

    return redirect()
        ->back()
        ->with(
            'success',
            'Story Point berhasil dihapus'
        );
}

public function destroyPeriod($id)
    {
        Period::findOrFail($id)
            ->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                'Month and all related story point data deleted successfully'
            );
    }

}
