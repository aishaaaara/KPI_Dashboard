<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PerformanceInsight;
use App\Models\Period;
use App\Models\Member;
use App\Models\Workload;

class PerformanceInsightController extends Controller
{
    public function index(Request $request)
    {
        $selectedPeriod = $request->period_id;

        $member = Member::where(
            'user_id',
            auth()->id()
        )->first();

        if (!$member) {
            return back()->with(
                'error',
                'Data member tidak ditemukan'
            );
        }

        $periods = Period::latest()->get();

        $insights = PerformanceInsight::with([
                'member.position',
                'period'
            ])
            ->where('member_id', $member->id)
            ->where('is_sent', 1)
            ->when($selectedPeriod, function ($query) use ($selectedPeriod) {
                $query->where('period_id', $selectedPeriod);
            })
            ->get();

        // Auto-mark as read saat member memilih periode dan data muncul
        if ($selectedPeriod) {
            foreach ($insights as $insight) {
                if (!$insight->is_read) {
                    $insight->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
                }
            }
        }

        foreach ($insights as $insight) {
            $insight->workloadData = Workload::where('member_id', $insight->member_id)
                ->where('period_id', $insight->period_id)
                ->first();
        }

        return view(
            'member.performance-insight.index',
            compact(
                'insights',
                'periods',
                'selectedPeriod'
            )
        );
    }
}