<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Communication;
use App\Models\Member;
use App\Models\Period;

class CommunicationController extends Controller
{
    public function index(Request $request)
    {
        

        $periods = Period::orderBy('year', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        $selectedPeriod = $request->period_id ?? $periods->first()?->id;

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
            'member.communication.index',
            compact(
                'communications',
                'members',
                'periods',
                'selectedPeriod'
            )
        );
    }


}