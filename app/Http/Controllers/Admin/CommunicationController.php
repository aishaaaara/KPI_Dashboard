<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Communication;
use App\Models\Member;
use App\Models\Period;
use App\Services\NotificationService;

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
        ->get()
        ->map(function ($comm) {                          // ← tambah ini
            $comm->clarity_class       = $this->getScoreClass($comm->clarity);
            $comm->responsiveness_class = $this->getScoreClass($comm->responsiveness);
            $comm->collaboration_class = $this->getScoreClass($comm->collaboration);
            $comm->overall_class       = $this->getOverallClass($comm->overall_score);
            return $comm;
        });

    $existingPeriods = Period::select('month', 'year')->get();

    $communicationCounts = Communication::selectRaw('period_id, count(*) as total')
        ->groupBy('period_id')
        ->pluck('total', 'period_id');

    $members = Member::with(['position'])->get();

    return view('admin.communication.index', compact(
        'communications',
        'communicationCounts',
        'members',
        'periods',
        'selectedPeriod',
        'existingPeriods'
    ));
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
    $request->validate([
        'month' => 'required',
        'year'  => 'required|integer',
    ]);

    $selectedDate = \Carbon\Carbon::createFromDate(
        $request->year,
        date('n', strtotime($request->month)),
        1
    )->startOfMonth();

    if ($selectedDate->lt(now()->startOfMonth())) {
        return redirect()->back()->with('error', 'Cannot add a past period');
    }

    $exists = Period::where('month', $request->month)
        ->where('year', $request->year)
        ->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'Period already exists');
    }

    $period  = Period::create([
        'month' => $request->month,
        'year'  => $request->year,
    ]);

    $members = Member::all();

    foreach ($members as $member) {

        // Communication
        \App\Models\Communication::firstOrCreate(
            ['member_id' => $member->id, 'period_id' => $period->id],
            ['clarity' => 0, 'responsiveness' => 0, 'collaboration' => 0, 'overall_score' => 0, 'notes' => null]
        );

        // Story Point
        \App\Models\StoryPoint::firstOrCreate(
            ['member_id' => $member->id, 'period_id' => $period->id],
            ['target' => 0, 'totals' => 0, 'summary' => 0]
        );

        // Workload
        \App\Models\Workload::firstOrCreate(
            ['member_id' => $member->id, 'period_id' => $period->id],
            ['all_task' => 0, 'todo' => 0, 'progress' => 0, 'review' => 0, 'done' => 0]
        );
    }

    app(NotificationService::class)->notifyNewCommunicationPeriod(
        $period->month . ' ' . $period->year
    );

    return redirect()->back()->with('success', 'New period added successfully');
}

    public function update(Request $request, $id)
{
    $communication = Communication::findOrFail($id);
    $period        = Period::findOrFail($communication->period_id);

    if ($this->isPeriodLocked($period)) {
        return back()->with('error', 'Periode ini sudah terkunci dan tidak dapat diubah.');
    }

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
        Communication::where( 'period_id', $id )->delete();

        Period::findOrFail($id)->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                'Period deleted successfully'
            );
    }

    private function isPeriodLocked(Period $period): bool
{
    $periodDate = \Carbon\Carbon::createFromDate(
        $period->year,
        date('n', strtotime($period->month)),
        1
    );

    // Terkunci kalau bulannya sudah lewat dari bulan sekarang
    return $periodDate->lt(now()->startOfMonth());
}

    private function getScoreClass(float $score): string
    {
        if ($score >= 90) return 'green';
        if ($score >= 80) return 'yellow';
        return 'red';
    }

    private function getOverallClass(float $score): string
    {
        if ($score >= 90) return 'score-good';
        if ($score >= 80) return 'score-medium';
        return 'score-low';
    }
}