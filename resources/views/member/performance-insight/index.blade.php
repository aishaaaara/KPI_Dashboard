@extends('member.layouts.app')

@section('content')

{{-- ===================== HEADER ===================== --}}
<div class="pi-header">
    <div class="pi-header-text">
        <h2>My Performance Insight</h2>
        <p>View your performance evaluation and recommendations</p>
    </div>
</div>

{{-- ===================== TOOLBAR ===================== --}}
<div class="pi-toolbar">
    <form method="GET" class="toolbar-form">

        <select
            name="period_id"
            class="period-select"
            onchange="this.form.submit()"
            aria-label="Pilih periode">

            <option value="">-- Pilih Bulan --</option>

            @foreach ($periods as $period)
                <option
                    value="{{ $period->id }}"
                    {{ $selectedPeriod == $period->id ? 'selected' : '' }}>
                    {{ $period->month }} {{ $period->year }}
                </option>
            @endforeach

        </select>

    </form>
</div>

{{-- ===================== MAIN CONTENT ===================== --}}
@if ($insights->isEmpty())

    <div class="empty-state">
        <i class="bi bi-bar-chart-line"></i>
        <p>Pilih periode untuk menampilkan data performance kamu</p>
    </div>

@else

    <div class="member-grid">

        @foreach ($insights as $insight)

            @php
                $comm    = $insight->communication_score;
                $sp      = $insight->story_point_score;
                $wl      = $insight->workload_score;
                $overall = $insight->overall_score;

                [$statusLabel, $statusClass] = match (true) {
                    $overall >= 90 => ['Excellent',    'badge--success'],
                    $overall >= 80 => ['Good',          'badge--primary'],
                    $overall >= 70 => ['Need Improve',  'badge--warning'],
                    default        => ['Critical',      'badge--danger'],
                };

                [$recClass, $recIcon, $recText] = match ($insight->recommendation) {
                    'Excellent Performance' => [
                        'rec--success', 'bi-check-circle',
                        'Performa luar biasa! Pertahankan konsistensi dan jadilah mentor tim.',
                    ],
                    'Good Performance' => [
                        'rec--info', 'bi-lightbulb',
                        'Performa baik. Identifikasi satu aspek terlemah dan fokus perbaiki bulan depan.',
                    ],
                    'Need Improvement' => [
                        'rec--warning', 'bi-exclamation-triangle',
                        'Beberapa aspek KPI perlu ditingkatkan. Diskusikan hambatan dengan tim lead secepatnya.',
                    ],
                    default => [
                        'rec--danger', 'bi-exclamation-circle',
                        'KPI di bawah target secara keseluruhan. Diperlukan evaluasi mendalam bersama manajer.',
                    ],
                };

                $barComm = $comm >= 85 ? 'fill--success' : ($comm >= 70 ? 'fill--warning' : 'fill--danger');
                $barSp   = $sp   >= 85 ? 'fill--success' : ($sp   >= 70 ? 'fill--warning' : 'fill--danger');
                $barWl   = $wl   >= 85 ? 'fill--success' : ($wl   >= 70 ? 'fill--warning' : 'fill--danger');

                $nameParts = explode(' ', $insight->member->name);
                $initials  = strtoupper(
                    substr($nameParts[0], 0, 1) .
                    (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '')
                );
            @endphp

            <div class="m-card" id="card-{{ $insight->id }}">

                {{-- Header --}}
                <div class="m-card-top">

                    <div class="m-avatar">{{ $initials }}</div>

                    <div class="m-info">
                        <div class="m-name">{{ $insight->member->name }}</div>
                        <div class="m-pos">{{ $insight->member->position->name }}</div>
                    </div>

                    <div class="m-meta">
                        <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        <span class="sent-pill">
                            <i class="bi bi-send-check-fill"></i>
                            {{ \Carbon\Carbon::parse($insight->sent_at)->format('d M Y') }}
                        </span>
                    </div>

                </div>

                {{-- KPI Score Bars --}}
                <div class="score-rows">

                    <div class="score-row">
                        <span class="score-label">Communication</span>
                        <div class="score-bar-bg">
                            <div class="score-bar-fill {{ $barComm }}" style="width: {{ min($comm, 100) }}%"></div>
                        </div>
                        <span class="score-pct">{{ $comm }}%</span>
                    </div>

                    <div class="score-row">
                        <span class="score-label">Story Point</span>
                        <div class="score-bar-bg">
                            <div class="score-bar-fill {{ $barSp }}" style="width: {{ min($sp, 100) }}%"></div>
                        </div>
                        <span class="score-pct">{{ $sp }}%</span>
                    </div>

                    <div class="score-row">
                        <span class="score-label">Workload</span>
                        <div class="score-bar-bg">
                            <div class="score-bar-fill {{ $barWl }}" style="width: {{ min($wl, 100) }}%"></div>
                        </div>
                        <span class="score-pct">{{ $wl }}%</span>
                    </div>

                </div>

                {{-- Overall Score --}}
                <div class="overall-row">
                    <span class="overall-label">Overall KPI</span>
                    <div class="overall-bar-bg">
                        <div
                            class="overall-bar-fill"
                            style="width: {{ min($overall, 100) }}%; background: {{ $overall >= 85 ? '#22c55e' : ($overall >= 70 ? '#f59e0b' : '#ef4444') }}">
                        </div>
                    </div>
                    <span class="overall-pct">{{ $overall }}%</span>
                </div>

                {{-- Task Summary --}}
                <div class="task-summary">

                    <div class="task-box">
                        <div class="task-value">{{ $insight->workloadData->all_task ?? 0 }}</div>
                        <div class="task-label">All Task</div>
                    </div>

                    <div class="task-box success">
                        <div class="task-value">{{ $insight->workloadData->done ?? 0 }}</div>
                        <div class="task-label">Done</div>
                    </div>

                    <div class="task-box warning">
                        <div class="task-value">{{ $insight->workloadData->review ?? 0 }}</div>
                        <div class="task-label">Review</div>
                    </div>

                </div>

                {{-- Recommendation --}}
                <div class="rec-box {{ $recClass }}">
                    <i class="bi {{ $recIcon }}"></i>
                    <span>{{ $recText }}</span>
                </div>

                {{-- Admin Notes --}}
                @if ($insight->admin_notes)
                    <div class="admin-notes-box">
                        <i class="bi bi-chat-left-text-fill"></i>
                        <span>{{ $insight->admin_notes }}</span>
                    </div>
                @endif

            </div>

        @endforeach

    </div>

@endif

{{-- ===================== STYLES ===================== --}}
<style>

    /* ----- Header ----- */
    .pi-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .pi-header h2 {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .pi-header p {
        font-size: 13px;
        color: #98a2b3;
        margin: 4px 0 0;
    }

    /* ----- Toolbar ----- */
    .pi-toolbar {
        margin-bottom: 16px;
    }

    .toolbar-form {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .period-select {
        height: 38px;
        min-width: 180px;
        padding: 0 28px 0 10px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        font-size: 13px;
        background-color: #fff;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23888' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        cursor: pointer;
    }

    /* ----- Empty State ----- */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #b0b9c8;
        font-size: 13px;
    }

    .empty-state i {
        font-size: 36px;
        display: block;
        margin-bottom: 12px;
        opacity: .4;
    }

    /* ----- Member Grid ----- */
    .member-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 14px;
    }

    /* ----- Member Card ----- */
    .m-card {
        background: #fff;
        border: 1px solid #edf0f5;
        border-radius: 20px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: border-color .15s, box-shadow .15s;
    }

    /* ----- Card Top ----- */
    .m-card-top {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .m-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #dbeafe;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .m-info { flex: 1; min-width: 0; }
    .m-name { font-size: 13px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .m-pos  { font-size: 11px; color: #98a2b3; margin-top: 1px; }

    .m-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
        flex-shrink: 0;
    }

    /* ----- Badges ----- */
    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge--success { background: #dcfce7; color: #15803d; }
    .badge--primary { background: #dbeafe; color: #1d4ed8; }
    .badge--warning { background: #fef3c7; color: #b45309; }
    .badge--danger  { background: #fee2e2; color: #dc2626; }

    .sent-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 999px;
        background: #f0fdf4;
        color: #15803d;
        font-size: 10px;
        font-weight: 600;
    }

    /* ----- Score Bars ----- */
    .score-rows { display: flex; flex-direction: column; gap: 7px; }

    .score-row  { display: flex; align-items: center; gap: 8px; }
    .score-label { font-size: 11px; color: #98a2b3; width: 95px; flex-shrink: 0; }
    .score-pct   { font-size: 11px; font-weight: 600; color: #374151; width: 36px; text-align: right; flex-shrink: 0; }

    .score-bar-bg {
        flex: 1;
        height: 5px;
        border-radius: 999px;
        background: #f1f5f9;
        overflow: hidden;
    }

    .score-bar-fill {
        height: 100%;
        border-radius: 999px;
        transition: width .4s ease;
    }

    .fill--success { background: #22c55e; }
    .fill--warning { background: #f59e0b; }
    .fill--danger  { background: #ef4444; }

    /* ----- Overall Row ----- */
    .overall-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 0;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
    }

    .overall-label { font-size: 11px; font-weight: 600; color: #374151; width: 95px; flex-shrink: 0; }
    .overall-pct   { font-size: 13px; font-weight: 700; color: #111827; width: 36px; text-align: right; flex-shrink: 0; }

    .overall-bar-bg {
        flex: 1;
        height: 7px;
        border-radius: 999px;
        background: #f1f5f9;
        overflow: hidden;
    }

    .overall-bar-fill {
        height: 100%;
        border-radius: 999px;
        transition: width .4s ease;
    }

    /* ----- Task Summary ----- */
    .task-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .task-box {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
    }

    .task-box.success { background: #f0fdf4; }
    .task-box.warning { background: #fffbeb; }

    .task-value {
        font-size: 22px;
        font-weight: 700;
        color: #111827;
        line-height: 1;
        margin-bottom: 4px;
    }

    .task-box.success .task-value { color: #16a34a; }
    .task-box.warning .task-value { color: #d97706; }

    .task-label {
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
    }

    /* ----- Recommendation ----- */
    .rec-box {
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 12px;
        line-height: 1.55;
        display: flex;
        gap: 7px;
        align-items: flex-start;
    }

    .rec-box i { font-size: 14px; flex-shrink: 0; margin-top: 1px; }

    .rec--success { background: #f0fdf4; color: #166534; }
    .rec--info    { background: #eff6ff; color: #1e40af; }
    .rec--warning { background: #fffbeb; color: #92400e; }
    .rec--danger  { background: #fef2f2; color: #991b1b; }

    /* ----- Admin Notes ----- */
    .admin-notes-box {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 12px;
        color: #374151;
        line-height: 1.55;
    }

    .admin-notes-box i {
        font-size: 13px;
        color: #6b7280;
        flex-shrink: 0;
        margin-top: 1px;
    }

</style>

@endsection