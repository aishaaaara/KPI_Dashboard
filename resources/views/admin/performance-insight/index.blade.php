@extends('admin.layouts.app')

@section('content')

    {{-- ===================== HEADER ===================== --}}
    <div class="pi-header">
        <div class="pi-header-text">
            <h2>Performance Insight</h2>
            <p>Generate dan kirim laporan KPI per anggota tim</p>
        </div>
    </div>

    {{-- ===================== TOOLBAR ===================== --}}
    <div class="pi-toolbar">

        <div class="toolbar-form">

            {{-- Dropdown period: saat berubah, reload halaman dulu --}}
            <select
                id="period-select"
                class="period-select"
                onchange="onPeriodChange(this.value)"
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

            {{-- Form generate — period_id diisi via JS --}}
            <form
                method="POST"
                action="{{ route('performance-insight.generate') }}"
                id="generate-form">

                @csrf
                <input type="hidden" name="period_id" id="generate-period-id" value="{{ $selectedPeriod }}">

            <button type="submit" class="btn-generate">
                <i class="bi bi-lightning-charge-fill"></i>
                Generate
            </button>

            </form>

        </div>

    </div>



{{-- ===================== MAIN CONTENT ===================== --}}
@if ($insights->isEmpty())

    <div class="empty-state">
        <i class="bi bi-bar-chart-line"></i>
        <p>Pilih periode dan klik <strong>Generate</strong> untuk menampilkan data</p>
    </div>

@else

    {{-- Send Form wraps the whole card grid --}}
    <form
        method="POST"
        action="{{ route('performance-insight.send') }}"
        id="send-form">

        @csrf

        {{-- Sticky send bar --}}
        <div class="send-bar" id="send-bar">

            <div class="send-bar-left">
                <label class="check-all-wrap" for="check-all">
                    <input
                        type="checkbox"
                        id="check-all"
                        onchange="toggleAll(this.checked)"
                        aria-label="Pilih semua">
                    Pilih semua
                </label>
                <span class="selected-count" id="selected-count">0 dipilih</span>
            </div>

            <div class="send-bar-right">
                <button
                    type="button"
                    class="btn-notes-toggle"
                    onclick="toggleNotesPanel()"
                    id="notes-toggle-btn">
                    <i class="bi bi-chat-left-text"></i>
                    Tambah catatan
                </button>
                <button
                    type="submit"
                    class="btn-send"
                    id="btn-send"
                    disabled>
                    <i class="bi bi-send-fill"></i>
                    Kirim
                    <span class="send-badge" id="send-badge">0</span>
                </button>
            </div>

        </div>

        {{-- Admin Notes Panel --}}
        <div class="notes-panel" id="notes-panel">
            <label class="notes-label" for="admin-notes">
                Catatan admin (dikirim ke semua member yang dipilih)
            </label>
            <textarea
                name="admin_notes"
                id="admin-notes"
                rows="3"
                maxlength="500"
                placeholder="Opsional — tulis catatan atau instruksi tambahan..."
                oninput="updateNotesCount()"></textarea>
            <div class="notes-footer">
                <span class="char-count" id="notes-char">0/500</span>
            </div>
        </div>

        {{-- Member Grid --}}
        <div class="member-grid">

            @foreach ($insights as $insight)

                @php
                    $comm    = $insight->communication_score;
                    $sp      = $insight->story_point_score;
                    $wl      = $insight->workload_score;
                    $overall = $insight->overall_score;

                    [$statusLabel, $statusClass] = match (true) {
                        $overall >= 90 => ['Excellent',   'badge--success'],
                        $overall >= 80 => ['Good',         'badge--primary'],
                        $overall >= 70 => ['Need Improve', 'badge--warning'],
                        default        => ['Critical',     'badge--danger'],
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

                <div
                    class="m-card {{ $insight->is_sent ? 'm-card--sent' : '' }}"
                    id="card-{{ $insight->id }}">

                    {{-- Checkbox + Header --}}
                <div class="m-card-top">

                    @php
                        $canResend = $insight->is_sent &&
                                    !$insight->is_read &&
                                    $insight->sent_at &&
                                    \Carbon\Carbon::parse($insight->sent_at)->diffInMinutes(now()) >= 5;
                    @endphp
                    <input
                        type="checkbox"
                        name="selected[]"
                        value="{{ $insight->id }}"
                        class="m-checkbox"
                        {{ ($insight->is_sent && $insight->is_read) || ($insight->is_sent && !$canResend) ? 'disabled' : '' }}
                        onchange="syncSelection()"
                        aria-label="Pilih {{ $insight->member->name }}">

                    <div class="m-avatar">{{ $initials }}</div>

                    <div class="m-info">
                        <div class="m-name">{{ $insight->member?->name ?? 'Unknown' }}</div>
                        <div class="m-pos">{{ $insight->member->position?->name ?? '-' }}</div>
                    </div>

                    <div class="m-meta">
                        <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        @if ($insight->is_sent)
                            <span class="sent-pill">
                                <i class="bi bi-send-check-fill"></i>
                                Terkirim
                            </span>
                            @if ($insight->is_read)
                                <span class="read-pill read-pill--read">
                                    <i class="bi bi-eye-fill"></i>
                                    Dibaca {{ \Carbon\Carbon::parse($insight->read_at)->diffForHumans() }}
                                </span>
                            @else
                                <span class="read-pill read-pill--unread">
                                    <i class="bi bi-eye-slash"></i>
                                    Belum dibaca
                                </span>
                            @endif
                        @endif
                    </div>

                </div>

                    {{-- KPI Score Bars --}}
                    <div class="score-rows">

                        <div class="score-row">
                            <span class="score-label">Communication</span>
                            <div class="score-bar-bg">
                                <div
                                    class="score-bar-fill {{ $barComm }}"
                                    style="width: {{ min($comm, 100) }}%">
                                </div>
                            </div>
                            <span class="score-pct">{{ $comm }}%</span>
                        </div>

                        <div class="score-row">
                            <span class="score-label">Story Point</span>
                            <div class="score-bar-bg">
                                <div
                                    class="score-bar-fill {{ $barSp }}"
                                    style="width: {{ min($sp, 100) }}%">
                                </div>
                            </div>
                            <span class="score-pct">{{ $sp }}%</span>
                        </div>

                        <div class="score-row">
                            <span class="score-label">Workload</span>
                            <div class="score-bar-bg">
                                <div
                                    class="score-bar-fill {{ $barWl }}"
                                    style="width: {{ min($wl, 100) }}%">
                                </div>
                            </div>
                            <span class="score-pct">{{ $wl }}%</span>
                        </div>

                    </div>

                    {{-- Overall Score --}}
                    <div class="overall-row">
                        <span class="overall-label">Overall KPI</span>
                        <div class="overall-bar-bg">
                            <div
                                class="overall-bar-fill {{ $barComm }}"
                                style="width: {{ min($overall, 100) }}%; background: {{ $overall >= 85 ? '#22c55e' : ($overall >= 70 ? '#f59e0b' : '#ef4444') }}">
                            </div>
                        </div>
                        <span class="overall-pct">{{ $overall }}%</span>
                    </div>

                    {{-- TASK SUMMARY --}}
                    <div class="task-summary">

                        <div class="task-box">

                            <div class="task-value">

                                {{ $insight->workloadData->all_task ?? 0 }}

                            </div>

                            <div class="task-label">

                                All Task

                            </div>

                        </div>

                        <div class="task-box success">

                            <div class="task-value">

                                {{ $insight->workloadData->done ?? 0 }}

                            </div>

                            <div class="task-label">

                                Done

                            </div>

                        </div>

                        <div class="task-box warning">

                            <div class="task-value">

                                {{ $insight->workloadData->review ?? 0 }}

                            </div>

                            <div class="task-label">

                                Review

                            </div>

                        </div>

                    </div>

                    {{-- Recommendation --}}
                    <div class="rec-box {{ $recClass }}">
                        <i class="bi {{ $recIcon }}"></i>
                        <span>{{ $recText }}</span>
                    </div>


                    {{-- Sent info --}}
                    @if ($insight->is_sent && $insight->sent_at)
                        <div class="sent-info">
                            <i class="bi bi-clock-history"></i>
                            Dikirim {{ \Carbon\Carbon::parse($insight->sent_at)->diffForHumans() }}
                            @if ($insight->admin_notes)
                                · <em>"{{ Str::limit($insight->admin_notes, 60) }}"</em>
                            @endif
                        </div>
                    @endif

                </div>

            @endforeach

        </div>

    </form>

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
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
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

    .btn-generate {
        height: 38px;
        padding: 0 16px;
        border: none;
        border-radius: 12px;
        background: #111827;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        transition: background .15s;
    }

    .btn-generate:hover { background: #1f2937; }

    /* ----- Success Alert ----- */
    .alert-success-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        margin-bottom: 16px;
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

    /* ----- Sticky Send Bar ----- */
    .send-bar {
        position: sticky;
        top: 0;
        z-index: 100;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
    }

    .send-bar-left  { display: flex; align-items: center; gap: 12px; }
    .send-bar-right { display: flex; align-items: center; gap: 8px; }

    .check-all-wrap {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #6b7280;
        cursor: pointer;
        user-select: none;
    }

    .check-all-wrap input {
        width: 15px;
        height: 15px;
        accent-color: #2563eb;
        cursor: pointer;
    }

    .selected-count {
        font-size: 12px;
        color: #b0b9c8;
    }

    /* ----- Buttons ----- */
    .btn-notes-toggle {
        height: 36px;
        padding: 0 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        color: #374151;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: background .15s;
    }

    .btn-notes-toggle:hover { background: #f3f4f6; }

    .btn-send {
        height: 36px;
        padding: 0 16px;
        border: none;
        border-radius: 10px;
        background: #2563eb;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        transition: opacity .15s;
    }

    .btn-send:disabled { opacity: .4; cursor: not-allowed; }
    .btn-send:not(:disabled):hover { opacity: .85; }

    .send-badge {
        background: rgba(255,255,255,.25);
        border-radius: 999px;
        padding: 1px 7px;
        font-size: 11px;
    }

    /* ----- Notes Panel ----- */
    .notes-panel {
        display: none;
        flex-direction: column;
        gap: 7px;
        background: #f7f8fc;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px 16px;
        margin-bottom: 14px;
    }

    .notes-panel.open { display: flex; }

    .notes-label {
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }

    .notes-panel textarea {
        resize: none;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 9px 12px;
        font-size: 13px;
        font-family: inherit;
        color: #374151;
        background: #fff;
        line-height: 1.55;
        outline: none;
    }

    .notes-panel textarea:focus { border-color: #2563eb; }

    .notes-footer { display: flex; justify-content: flex-end; }
    .char-count   { font-size: 11px; color: #b0b9c8; }

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

    .m-card:has(.m-checkbox:checked) {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }

    .m-card--sent {
        opacity: .75;
        background: #fafafa;
    }

    /* ----- Card Top ----- */
    .m-card-top {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .m-checkbox {
        width: 15px;
        height: 15px;
        accent-color: #2563eb;
        cursor: pointer;
        flex-shrink: 0;
        margin-top: 3px;
    }

    .m-checkbox:disabled { cursor: not-allowed; }

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
    .score-rows {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .score-row { display: flex; align-items: center; gap: 8px; }
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

/* ----- TASK SUMMARY ----- */

.task-summary{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:10px;
    margin:12px 0;
}

.task-box{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:12px;
    text-align:center;
}

.task-box.success{
    background:#f0fdf4;
}

.task-box.warning{
    background:#fffbeb;
}

.task-value{
    font-size:22px;
    font-weight:700;
    color:#111827;
    line-height:1;
    margin-bottom:4px;
}

.task-box.success .task-value{
    color:#16a34a;
}

.task-box.warning .task-value{
    color:#d97706;
}

.task-label{
    font-size:11px;
    color:#6b7280;
    font-weight:500;
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

    /* ----- Sent Info ----- */
    .sent-info {
        font-size: 11px;
        color: #b0b9c8;
        display: flex;
        align-items: flex-start;
        gap: 5px;
        flex-wrap: wrap;
        line-height: 1.5;
    }

    .sent-info em { color: #9ca3af; font-style: italic; }
    .read-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 600;
    }

    .read-pill--read {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .read-pill--unread {
        background: #fef3c7;
        color: #b45309;
    }
    
</style>

{{-- ===================== SCRIPTS ===================== --}}
<script>

    /* ----- Selection sync ----- */
    function syncSelection() {
        const checkboxes = document.querySelectorAll('.m-checkbox:not(:disabled)');
        const checked    = document.querySelectorAll('.m-checkbox:checked');
        const allCb      = document.getElementById('check-all');
        const countEl    = document.getElementById('selected-count');
        const badge      = document.getElementById('send-badge');
        const sendBtn    = document.getElementById('btn-send');

        const n = checked.length;
        const t = checkboxes.length;

        if (allCb) {
            allCb.checked       = n === t && t > 0;
            allCb.indeterminate = n > 0 && n < t;
        }

        if (countEl) countEl.textContent = n + ' dipilih';
        if (badge)   badge.textContent   = n;
        if (sendBtn) sendBtn.disabled    = n === 0;
    }

    function toggleAll(checked) {
        document.querySelectorAll('.m-checkbox:not(:disabled)').forEach(cb => {
            cb.checked = checked;
        });
        syncSelection();
    }

    /* ----- Notes panel ----- */
    function toggleNotesPanel() {
        const panel = document.getElementById('notes-panel');
        const btn   = document.getElementById('notes-toggle-btn');
        const open  = panel.classList.toggle('open');
        btn.style.background = open ? '#eff6ff' : '';
        btn.style.color      = open ? '#1d4ed8' : '';
        btn.style.border     = open ? '1px solid #bfdbfe' : '';
    }

    function updateNotesCount() {
        const ta = document.getElementById('admin-notes');
        const cc = document.getElementById('notes-char');
        if (ta && cc) cc.textContent = ta.value.length + '/500';
    }

    /* ----- Init ----- */
    syncSelection();
    function onPeriodChange(periodId) {
    if (!periodId) return;
    // Reload halaman dengan period baru supaya $periodLocked dihitung ulang
    window.location.href = '{{ route('performance-insight.index') }}?period_id=' + periodId;
}
</script>

@endsection