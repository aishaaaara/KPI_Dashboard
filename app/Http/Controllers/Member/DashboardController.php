<?php

namespace App\Http\Controllers\Member;

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

        // ── Hitung KPI per member ──────────────────────────────
        foreach ($members as $member) {

            $communication = Communication::where('member_id', $member->id)
                ->where('period_id', $selectedPeriodId)
                ->first();

            $storyPoint = StoryPoint::where('member_id', $member->id)
                ->where('period_id', $selectedPeriodId)
                ->first();

            $workload = Workload::where('member_id', $member->id)
                ->where('period_id', $selectedPeriodId)
                ->first();

            // Skip member yang tidak punya data apapun di periode ini
            if (!$communication && !$storyPoint && !$workload) {
                continue;
            }

            $communicationScore = $communication->overall_score ?? 0;
            $storyPointScore    = $storyPoint->summary ?? 0;

            $workloadScore = 0;
            if ($workload && $workload->all_task > 0) {
                $workloadScore = round(($workload->done / $workload->all_task) * 100);
            }
            
            //menghitung total keseluruhan
            $averageKpi = round(
                ($communicationScore * 0.2) +
                ($storyPointScore    * 0.35) +
                ($workloadScore      * 0.45),
                2
            );

            $memberKpis->push([
                'member'        => $member,
                'communication' => $communicationScore,
                'story_point'   => $storyPointScore,
                'workload'      => $workloadScore,
                'average_kpi'   => $averageKpi,
            ]);
        }

        // ── Statistik ──────────────────────────────────────────
        $totalMembers = $memberKpis->count(); // total seluruh anggota tim
        $averageKpiScore = round($memberKpis->avg('average_kpi'), 2);

        $sortedKpis      = $memberKpis->sortByDesc('average_kpi')->values();
        $topPerformance  = $sortedKpis->first();
        $lowestPerformance = $sortedKpis->last();
        $highestKpi      = $topPerformance['average_kpi'] ?? 0;
        $lowestKpi       = $lowestPerformance['average_kpi'] ?? 0;

        // ── Risk Indicator ─────────────────────────────────────
        $greenMember = $memberKpis->filter(fn($i) => $i['average_kpi'] >= 90)->count();
        $yellowMember = $memberKpis->filter(fn($i) => $i['average_kpi'] >= 80 && $i['average_kpi'] < 90)->count();
        $redMember    = $memberKpis->filter(fn($i) => $i['average_kpi'] < 80)->count();

        // ── KPI Achievement ────────────────────────────────────
        $kpiAchievement = $memberKpis->where('average_kpi', '>=', 80)->count();
        $kpiAchievementPercent = $totalMembers > 0
            ? round(($kpiAchievement / $totalMembers) * 100)
            : 0;

        // ── KPI Trend ──────────────────────────────────────────
        foreach ($trendPeriods as $period) {

            $communications = Communication::where('period_id', $period->id)->get();
            $storyPoints    = StoryPoint::where('period_id', $period->id)->get();
            $workloads      = Workload::where('period_id', $period->id)->get();

            $memberIds = collect()
                ->merge($communications->pluck('member_id'))
                ->merge($storyPoints->pluck('member_id'))
                ->merge($workloads->pluck('member_id'))
                ->unique();

            $periodKpis = collect();

            foreach ($memberIds as $memberId) {

                $comm = $communications->where('member_id', $memberId)->first();
                $sp   = $storyPoints->where('member_id', $memberId)->first();
                $wl   = $workloads->where('member_id', $memberId)->first();

                $commScore = $comm->overall_score ?? 0;
                $spScore   = $sp->summary ?? 0;
                $wlScore   = 0;

                if ($wl && $wl->all_task > 0) {
                    $wlScore = round(($wl->done / $wl->all_task) * 100);
                }

                $periodKpis->push(
                    ($commScore * 0.2) +
                    ($spScore   * 0.35) +
                    ($wlScore   * 0.45)
                );
            }

            $kpiTrendLabels[] = substr($period->month, 0, 3) . ' ' . $period->year;
            $kpiTrendData[]   = $periodKpis->count() ? round($periodKpis->avg(), 2) : 0;
        }

        // ── Ranking (sudah sorted) ─────────────────────────────
        $memberKpis = $sortedKpis;

        return view('member.dashboard.index', compact(
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
        ));
    }
}