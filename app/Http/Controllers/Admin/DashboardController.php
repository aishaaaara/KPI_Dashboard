<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Communication;
use App\Models\StoryPoint;
use App\Models\Workload;
use App\Models\Period;

class DashboardController extends Controller
{
    public function index()
    {
        $selectedPeriodId = request('period_id');
        if (!$selectedPeriodId) {
            $latestPeriod = Period::latest('id')->first();
            $selectedPeriodId = $latestPeriod?->id;
        }

        $totalMembers = Member::count();
        $members = Member::with('position')->get();
        $memberKpis = collect();
        $periods = Period::latest()->get();
        $kpiTrendLabels = [];
        $kpiTrendData = [];

        $trendPeriods = Period::orderBy('year')
            ->orderByRaw("
                FIELD(month,
                'January','February','March','April',
                'May','June','July','August',
                'September','October','November','December')
            ")
            ->get();

        foreach ($members as $member) {

            $communication = Communication::where(
                    'member_id',
                    $member->id )
                ->where('period_id', $selectedPeriodId)
                ->first();

            $storyPoint = StoryPoint::where(
                    'member_id',
                    $member->id
                )
                ->where('period_id', $selectedPeriodId)
                ->first();

            $workload = Workload::where(
                        'member_id',
                        $member->id
                    )
                    ->where('period_id', $selectedPeriodId)
                    ->first();

            // Communication
            $communicationScore =
                $communication->overall_score ?? 0;

            // Story Point
            $storyPointScore =
                $storyPoint->summary ?? 0;

            // Workload
            $workloadScore = 0;

            if (
                $workload &&
                $workload->all_task > 0
            ) {

                $workloadScore = round(
                    ($workload->done / $workload->all_task) * 100
                );
            }

            $averageKpi = round(
                (
                    $communicationScore +
                    $storyPointScore +
                    $workloadScore
                ) / 3,
                2
            );

            $memberKpis->push([
                'member' => $member,
                'communication' => $communicationScore,
                'story_point' => $storyPointScore,
                'workload' => $workloadScore,
                'average_kpi' => $averageKpi,
            ]);
        }

        $averageKpiScore = round(
            $memberKpis->avg('average_kpi'),
            2
        );


        $sortedKpis = $memberKpis
    ->sortByDesc('average_kpi')
    ->values();

$topPerformance = $sortedKpis->first();

$lowestPerformance = $sortedKpis->last();

$highestKpi =
    $topPerformance['average_kpi'] ?? 0;

$lowestKpi =
    $lowestPerformance['average_kpi'] ?? 0;

/*
|--------------------------------------------------------------------------
| Risk Indicator
|--------------------------------------------------------------------------
*/

$greenMember = $memberKpis
    ->filter(function ($item) {
        return $item['average_kpi'] >= 90;
    })
    ->count();

$yellowMember = $memberKpis
    ->filter(function ($item) {
        return $item['average_kpi'] >= 80
            && $item['average_kpi'] < 90;
    })
    ->count();

$redMember = $memberKpis
    ->filter(function ($item) {
        return $item['average_kpi'] < 80;
    })
    ->count();

/*
|--------------------------------------------------------------------------
| KPI Achievement
|--------------------------------------------------------------------------
*/

$kpiAchievement = $memberKpis
    ->where('average_kpi', '>=', 80)
    ->count();

$kpiAchievementPercent =
    $totalMembers > 0
    ? round(
        ($kpiAchievement / $totalMembers) * 100
    )
    : 0;
    
foreach ($trendPeriods as $period) {

    $communications = Communication::where(
        'period_id',
        $period->id
    )->get();

    $storyPoints = StoryPoint::where(
        'period_id',
        $period->id
    )->get();

    $workloads = Workload::where(
        'period_id',
        $period->id
    )->get();

    $memberIds = collect()
        ->merge($communications->pluck('member_id'))
        ->merge($storyPoints->pluck('member_id'))
        ->merge($workloads->pluck('member_id'))
        ->unique();

    $periodKpis = collect();

    foreach ($memberIds as $memberId) {

        $communication =
            $communications
                ->where('member_id', $memberId)
                ->first();

        $storyPoint =
            $storyPoints
                ->where('member_id', $memberId)
                ->first();

        $workload =
            $workloads
                ->where('member_id', $memberId)
                ->first();

        $communicationScore =
            $communication->overall_score ?? 0;

        $storyPointScore =
            $storyPoint->summary ?? 0;

        $workloadScore = 0;

        if (
            $workload &&
            $workload->all_task > 0
        ) {

            $workloadScore = round(
                ($workload->done /
                 $workload->all_task) * 100
            );
        }

        $averageKpi = (
            $communicationScore +
            $storyPointScore +
            $workloadScore
        ) / 3;

        $periodKpis->push($averageKpi);
    }

        $teamAverage =
            $periodKpis->count()
            ? round($periodKpis->avg(), 2)
            : 0;

        $kpiTrendLabels[] =
            substr($period->month, 0, 3)
            . ' '
            . $period->year;

        $kpiTrendData[] = $teamAverage;
    }

        return view(
            'admin.dashboard.index',
           compact(
                'totalMembers',
                'selectedPeriodId',
                'averageKpiScore',
                'topPerformance',
                'highestKpi',
                'lowestKpi',
                'greenMember',
                'yellowMember',
                'redMember',
                'kpiAchievementPercent',
                'memberKpis',
                'periods',
                'kpiTrendLabels',
                'kpiTrendData'
)
        );
    }
}