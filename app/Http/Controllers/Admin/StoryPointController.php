<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoryPoint;
use App\Models\Member;
use App\Models\Period;
use App\Services\NotificationService;


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

    // Untuk badge count — semua data tanpa filter
    $allStoryPoints = StoryPoint::select('period_id')->get();

    $members = Member::all();

    return view(
        'admin.story-points.index',
        compact(
            'storyPoints',
            'allStoryPoints',
            'members',
            'periods',
            'selectedPeriod',
            'existingPeriods'
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
        'summary' => $achievement,
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
    $exists = Period::where('month', $request->month)
        ->where('year', $request->year)
        ->exists();

    if (!$exists) {
        $period = Period::create([
            'month' => $request->month,
            'year'  => $request->year,
        ]);

        // Auto-create data 0 untuk semua member
        $members = Member::all();
        foreach ($members as $member) {
            StoryPoint::create([
                'member_id' => $member->id,
                'period_id' => $period->id,
                'target'    => 0,
                'totals'    => 0,
                'summary'   => 0,
            ]);
        }

        app(NotificationService::class)->notifyNewStoryPointPeriod(
            $period->month . ' ' . $period->year
        );
    }

    return redirect()->back()->with('success', 'New month added successfully');
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
