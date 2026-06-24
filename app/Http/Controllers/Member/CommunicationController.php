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
        $periods = Period::selectRaw("*, 
            FIELD(month, 'January','February','March','April','May','June',
            'July','August','September','October','November','December') as month_order")
            ->orderBy('year', 'asc')
            ->orderBy('month_order', 'asc')
            ->get();

        $selectedPeriod = $request->period_id ?? $periods->last()?->id; // ← last() bukan first() supaya default ke terbaru

        $communications = Communication::with(['member.position', 'period'])
            ->when($selectedPeriod, function ($query) use ($selectedPeriod) {
                $query->where('period_id', $selectedPeriod);
            })
            ->latest()
            ->get();

        $existingPeriods = Period::select('month', 'year')->get();

        $communicationCounts = Communication::selectRaw('period_id, count(*) as total')
            ->groupBy('period_id')
            ->pluck('total', 'period_id');

        $members = Member::with(['position'])->get();

        return view('member.communication.index', compact(
            'communications',
            'communicationCounts',
            'members',
            'periods',
            'selectedPeriod',
            'existingPeriods'
        ));
    }
}