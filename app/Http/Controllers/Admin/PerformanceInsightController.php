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
use App\Services\NotificationService;

class PerformanceInsightController extends Controller
{

    public function index(Request $request)
{
    $periods = Period::latest()->get();

    $selectedPeriod = $request->period_id ?? optional($periods->first())->id;

    $insights = PerformanceInsight::with([
        'member.position',
        'period'
            ])
            ->whereHas('member')
            ->whereHas('period') // ← tambah ini
            ->when($selectedPeriod, function ($query) use ($selectedPeriod) {
                $query->where('period_id', $selectedPeriod);
            })
            ->get()
            ->unique('member_id');
    
    foreach ($insights as $insight) {
        $insight->workloadData = Workload::where('member_id', $insight->member_id)
            ->where('period_id', $insight->period_id)
            ->first();
    }


    return view(
        'admin.performance-insight.index',
        compact('insights', 'periods', 'selectedPeriod')
    );
}

    public function generate(Request $request)
{
    $periodId = $request->period_id;

    // Member yang insight-nya sudah terkirim TIDAK dihapus/diregenerate.
    // Hanya insight yang belum terkirim (is_sent = 0) yang dihapus & dibuat ulang.
    $sentMemberIds = PerformanceInsight::where('period_id', $periodId)
        ->where('is_sent', 1)
        ->pluck('member_id');

    PerformanceInsight::where('period_id', $periodId)
        ->where('is_sent', 0)
        ->delete();

    // Skip member yang insight-nya sudah terkirim, biar data yang sudah
    // dilihat member tidak berubah diam-diam.
    $members = Member::whereNotIn('id', $sentMemberIds)->get();

    foreach($members as $member)
    {
        $communication =
            Communication::where('member_id', $member->id)
                ->where('period_id', $periodId)
                ->first();

        $storyPoint =
            StoryPoint::where('member_id', $member->id)
                ->where('period_id', $periodId)
                ->first();

        $workload =
            Workload::where('member_id', $member->id)
                ->where('period_id', $periodId)
                ->first();

        if(!$communication || !$storyPoint || !$workload){
            continue;
        }

        $communicationScore = $communication->overall_score;

        $storyPointScore =
            $storyPoint->target > 0
            ? ($storyPoint->totals / $storyPoint->target) * 100
            : 0;

        $workloadScore =
            $workload->all_task > 0
            ? ($workload->done / $workload->all_task) * 100
            : 0;

        $overall =
            ($communicationScore + $storyPointScore + $workloadScore) / 3;

        $recommendation = $this->getRecommendation($overall);

        PerformanceInsight::create([
            'member_id' => $member->id,
            'period_id' => $periodId,
            'communication_score' => round($communicationScore, 2),
            'story_point_score' => round($storyPointScore, 2),
            'workload_score' => round($workloadScore, 2),
            'overall_score' => round($overall, 2),
            'recommendation' => $recommendation,
            'is_sent' => 0
        ]);
    }

    $skippedCount = $sentMemberIds->count();
    $message = 'Performance Insight berhasil digenerate';
    if ($skippedCount > 0) {
        $message .= " ($skippedCount insight yang sudah terkirim tidak diubah)";
    }

    return redirect()
        ->route('performance-insight.index', ['period_id' => $periodId])
        ->with('success', $message);
}
    
   public function send(Request $request)
{
    $ids = $request->selected ?? [];

    $insights = PerformanceInsight::with(['member.user', 'period'])
        ->whereIn('id', $ids)
        ->where(function ($q) {
            $q->where('is_sent', 0)
              ->orWhere(function ($q2) {
                  $q2->where('is_read', 0)
                     ->where('sent_at', '<=', now()->subMinutes(5)); //muncul resend saaat sudah 5 menit
                                            // now()->subHours(24)); kalo mau berubah menjadi setiap 24 jam bisa ganti ini
              });
        })
        ->get();

    if ($insights->isEmpty()) {
        return redirect()->back()->with(
            'error',
            'Semua insight sudah dibaca atau belum melewati 5 menit sejak pengiriman terakhir'
        );
    }

    foreach ($insights as $insight) {
        $insight->update([
            'is_sent'     => 1,
            'sent_at'     => now(),
            'admin_notes' => $request->admin_notes,
            'is_read'     => false,
            'read_at'     => null,
        ]);

        app(NotificationService::class)->notifyPerformanceInsightSent(
            $insight->member->user_id,
            $insight->period->month . ' ' . $insight->period->year
        );
    }

    return redirect()->back()->with('success', 'Insight berhasil dikirim');
}
    private function getRecommendation(float $overall): string
    {
        if ($overall >= 90) return 'Excellent Performance';
        if ($overall >= 80) return 'Good Performance';
        if ($overall >= 70) return 'Need Improvement';
        return 'Critical Performance';
    }


}