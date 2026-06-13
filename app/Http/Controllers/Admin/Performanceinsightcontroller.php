<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Communication;
use App\Models\StoryPoint;
use App\Models\Workload;
use App\Models\Period;
use App\Models\Member;
use App\Models\PerformanceInsight;

class PerformanceInsightController extends Controller
{
    public function index(Request $request)
{
    $selectedPeriod = $request->period_id;

    $periods = Period::latest()->get();

    $insights = PerformanceInsight::with([
        'member.position',
        'period'
    ])

    ->when($selectedPeriod, function ($query) use ($selectedPeriod) {

        $query->where(
            'period_id',
            $selectedPeriod
        );

    })

    ->get();

    /*
    |--------------------------------------------------------------------------
    | Ambil workload untuk task summary
    |--------------------------------------------------------------------------
    */

    foreach ($insights as $insight) {

        $insight->workloadData = Workload::where(
            'member_id',
            $insight->member_id
        )
        ->where(
            'period_id',
            $insight->period_id
        )
        ->first();

    }

    return view(
        'admin.performance-insight.index',
        compact(
            'insights',
            'periods',
            'selectedPeriod'
        )
    );
}

    public function generate(Request $request)
    {
        $periodId = $request->period_id;

        PerformanceInsight::where(
            'period_id',
            $periodId
        )->delete();

        $members = Member::all();

        foreach($members as $member)
        {
            $communication =
                Communication::where(
                    'member_id',
                    $member->id
                )
                ->where(
                    'period_id',
                    $periodId
                )
                ->first();

            $storyPoint =
                StoryPoint::where(
                    'member_id',
                    $member->id
                )
                ->where(
                    'period_id',
                    $periodId
                )
                ->first();

            $workload =
                Workload::where(
                    'member_id',
                    $member->id
                )
                ->where(
                    'period_id',
                    $periodId
                )
                ->first();

            if(
                !$communication ||
                !$storyPoint ||
                !$workload
            ){
                continue;
            }

            $communicationScore =
                $communication->overall_score;

            $storyPointScore =
                $storyPoint->target > 0
                ? ($storyPoint->totals / $storyPoint->target) * 100
                : 0;

            $workloadScore =
                $workload->all_task > 0
                ? ($workload->done / $workload->all_task) * 100
                : 0;

            $overall =
                (
                    $communicationScore +
                    $storyPointScore +
                    $workloadScore
                ) / 3;

            if($overall >= 90)
            {
                $recommendation =
                    'Excellent Performance';
            }
            elseif($overall >= 80)
            {
                $recommendation =
                    'Good Performance';
            }
            elseif($overall >= 70)
            {
                $recommendation =
                    'Need Improvement';
            }
            else
            {
                $recommendation =
                    'Critical Performance';
            }

            PerformanceInsight::create([

                'member_id' => $member->id,

                'period_id' => $periodId,

                'communication_score' => round($communicationScore,2),

                'story_point_score' => round($storyPointScore,2),

                'workload_score' => round($workloadScore,2),

                'overall_score' => round($overall,2),

                'recommendation' => $recommendation,

                'is_sent' => 0

            ]);
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Performance Insight berhasil digenerate'
            );
    }

    public function send(Request $request)
    {
        PerformanceInsight::whereIn(
            'id',
            $request->selected ?? []
        )
        ->update([

            'is_sent' => 1,

            'sent_at' => now(),

            'admin_notes' => $request->admin_notes

        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Insight berhasil dikirim'
            );
    }
}