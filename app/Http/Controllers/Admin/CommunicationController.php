<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Communication;
use App\Models\Member;
use App\Models\Period;

class CommunicationController extends Controller
{
    public function index(Request $request)
    {
        $selectedPeriod = $request->period_id;

        $periods = Period::orderBy('year', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $communications = Communication::with([
                'member.position',
                'period'
            ])

            ->when($selectedPeriod, function ($query) use ($selectedPeriod) {

                $query->where(
                    'period_id',
                    $selectedPeriod
                );

            })

            ->latest()
            ->get();

        $members = Member::with([
            'position'
        ])->get();

        return view(
            'admin.communication.index',
            compact(
                'communications',
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
            'clarity' => 'required|numeric|min:0|max:100',
            'responsiveness' => 'required|numeric|min:0|max:100',
            'collaboration' => 'required|numeric|min:0|max:100',

        ]);

        $exists = Communication::where(

                'member_id',
                $request->member_id

            )

            ->where(

                'period_id',
                $request->period_id

            )

            ->exists();

        if($exists){

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Communication data already exists'
                );
        }

        $overall = (

            $request->clarity +
            $request->responsiveness +
            $request->collaboration

        ) / 3;

        Communication::create([

            'member_id' => $request->member_id,

            'period_id' => $request->period_id,

            'clarity' => $request->clarity,

            'responsiveness' => $request->responsiveness,

            'collaboration' => $request->collaboration,

            'overall_score' => round($overall, 2),

            'notes' => $request->notes

        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Communication data added successfully'
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

    public function update(Request $request, $id)
{
    $communication = Communication::findOrFail($id);

    $overall = (

        $request->clarity +
        $request->responsiveness +
        $request->collaboration

    ) / 3;

    $communication->update([

        'clarity' => $request->clarity,

        'responsiveness' => $request->responsiveness,

        'collaboration' => $request->collaboration,

        'overall_score' => round($overall, 2),

        'notes' => $request->notes

    ]);

    return redirect()
        ->back()
        ->with(
            'success',
            'Communication updated successfully'
        );
}

public function destroy($id)
{
    $communication =
        Communication::findOrFail($id);

    $communication->delete();

    return redirect()
        ->back()
        ->with(
            'success',
            'Communication deleted successfully'
        );
}

public function destroyPeriod($id)
{
    Communication::where(
        'period_id',
        $id
    )->delete();

    Period::findOrFail($id)->delete();

    return redirect()
        ->back()
        ->with(
            'success',
            'Period deleted successfully'
        );
}
}