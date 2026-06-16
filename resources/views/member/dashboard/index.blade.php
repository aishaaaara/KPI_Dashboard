@extends('member.layouts.app')

@section('content')

{{-- ===================== HEADER ===================== --}}
<div class="dashboard-header">

    <div class="header-title">
        <h2>Dashboard, Hi {{ auth()->user()->name }}!</h2>
        <p>KPI Monitoring Overview</p>
    </div>

    <div class="header-actions">

        <form method="GET">
            <select
                name="period_id"
                class="form-select period-filter"
                onchange="this.form.submit()">

            <option value="">Select Period</option>

                @foreach ($periods as $period)
                    <option
                        value="{{ $period->id }}"
                        {{ $selectedPeriodId == $period->id ? 'selected' : '' }}>
                        {{ $period->month }} {{ $period->year }}
                    </option>
                @endforeach

            </select>
        </form>

        <a href="{{ route('notifications.index') }}" class="btn-icon" title="Notifications" style="text-decoration:none; position:relative;">
            <i class="bi bi-bell-fill"></i>
            @php
                $unreadCount = \App\Models\Notification::forUser(auth()->id())->unread()->count();
            @endphp
            @if ($unreadCount > 0)
                <span class="bell-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
            @endif
        </a>

    </div>

</div>

{{-- ===================== KPI CARDS ===================== --}}
<div class="kpi-grid">

    <div class="kpi-card">
        <div class="kpi-label">Total Team Member</div>
        <div class="kpi-value">{{ $totalMembers }}</div>
        <div class="kpi-sub">Active Member</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">Average KPI Score</div>
        <div class="kpi-value">{{ $averageKpiScore }}%</div>
        <div class="kpi-sub">Team Average</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">Top Performance</div>
        <div class="kpi-value kpi-value--name">
            {{ $topPerformance['member']->name ?? '-' }}
        </div>
        <div class="kpi-sub">
            {{ $highestKpi }}% Average KPI
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">Lowest KPI</div>
        <div class="kpi-value">
            {{ $lowestKpi }}%
        </div>
        <div class="kpi-sub">
            Need Attention
        </div>
    </div>
</div>

<div class="dashboard-tabs">

    <button
        class="tab-btn"
        onclick="showTab('trend')">
        <i class="bi bi-graph-up"></i>
        KPI Trend
    </button>

    <button
        class="tab-btn active"
        onclick="showTab('ranking')">
        <i class="bi bi-trophy"></i>
        Ranking Member
    </button>

</div>

{{-- ===================== KPI TREND GRAFIK===================== --}}

<div
    id="trend-tab"
    class="tab-content">

    <div class="trend-card">

    <div class="trend-header">
        <h5>Team KPI Trend</h5>
        <span>
            Average KPI Team Performance
        </span>
    </div>

    <canvas id="kpiTrendChart"></canvas>

</div>
</div>

{{-- ===================== RANKING TABLE ===================== --}}
<div
    id="ranking-tab"
    class="tab-content active">

    <div class="table-card">
    <div class="table-responsive">
        <table class="rank-table">

            <thead>
                <tr>
                    <th style="width: 70px">Rank</th>
                    <th>Member</th>
                    <th style="width: 110px">Communication</th>
                    <th style="width: 110px">Story Point</th>
                    <th style="width: 110px">Workload</th>
                    <th style="width: 130px">Average KPI</th>
                    <th style="width: 160px">Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($memberKpis as $index => $item)

                    @php
                        $avg = $item['average_kpi'];

                        [$statusLabel, $statusClass] = match (true) {
                            $avg >= 90 => ['Excellent',         'badge--success'],
                            $avg >= 80 => ['Good',              'badge--primary'],
                            $avg >= 70 => ['Average',           'badge--warning'],
                            default    => ['Needs Improvement', 'badge--danger'],
                        };

                        $rankClass = match ($index) {
                            0 => 'rank--gold',
                            1 => 'rank--silver',
                            2 => 'rank--bronze',
                            default => '',
                        };
                    @endphp

                    <tr>

                        {{-- Rank --}}
                        <td>
                            <span class="rank-badge {{ $rankClass }}">
                                #{{ $index + 1 }}
                            </span>
                        </td>

                        {{-- Member --}}
                        <td>
                            <div class="member-name">{{ $item['member']->name }}</div>
                            <div class="member-pos">{{ $item['member']->position->name }}</div>
                        </td>

                        {{-- Scores --}}
                        <td class="score-cell">{{ round($item['communication']) }}%</td>
                        <td class="score-cell">{{ round($item['story_point']) }}%</td>
                        <td class="score-cell">{{ round($item['workload']) }}%</td>

                        {{-- KPI Progress --}}
                        <td>
                            <span class="kpi-pct">{{ round($avg) }}%</span>
                            <div class="progress-bar-wrap">
                                <div
                                    class="progress-bar-fill"
                                    style="width: {{ min($avg, 100) }}%">
                                </div>
                            </div>
                        </td>

                        {{-- Status Badge --}}
                        <td>
                            <span class="status-badge {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                    </tr>

                @endforeach
            </tbody>

        </table>
    </div>
</div>
</div>

{{-- ===================== STYLES ===================== --}}
<style>

    /* ----- Layout ----- */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 24px;
    }

    .header-title h2 {
        margin: 0;
        font-size: 26px;
        font-weight: 700;
        color: #111827;
    }

    .header-title p {
        margin: 4px 0 0;
        font-size: 13px;
        color: #98a2b3;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* ----- Period Filter ----- */
    .period-filter {
        height: 40px;
        min-width: 170px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        font-size: 13px;
    }

    /* ----- Buttons ----- */
    .btn-icon {
        width: 40px;
        height: 40px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.15s;
    }

    .btn-icon:hover {
        background: #f3f4f6;
    }

    .btn-primary {
        height: 40px;
        padding: 0 16px;
        border: none;
        border-radius: 12px;
        background: #2563eb;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        transition: background 0.15s;
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    /* ----- KPI Cards ----- */
    .kpi-grid{
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:16px;
        margin-bottom:20px;
    }
    @media(max-width:1200px){
    .kpi-grid{
        grid-template-columns:repeat(2,1fr);
    }
}

@media(max-width:768px){
    .kpi-grid{
        grid-template-columns:1fr;
    }
}

    .card-green{
        border-left:5px solid #22c55e;
    }

    .card-yellow{
        border-left:5px solid #f59e0b;
    }

    .card-red{
        border-left:5px solid #ef4444;
    }
    .kpi-card {
        background: #fff;
        border: 1px solid #edf0f5;
        border-radius: 20px;
        padding: 20px 22px;
        min-height: 130px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .kpi-label {
        font-size: 12px;
        color: #98a2b3;
        margin-bottom: 8px;
    }

    .kpi-value {
        font-size: 32px;
        font-weight: 700;
        color: #2563eb;
        line-height: 1.2;
    }

    .kpi-value--name {
        font-size: 20px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .kpi-sub {
        font-size: 12px;
        color: #98a2b3;
        margin-top: 5px;
    }

    /* ----- Table Card ----- */
    .table-card {
        background: #fff;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    .rank-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .rank-table thead tr {
        background: #f7f8fc;
    }

    .rank-table th {
        padding: 13px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: #98a2b3;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .rank-table td {
        padding: 16px;
        vertical-align: middle;
        border-top: 1px solid #f1f1f1;
    }

    .rank-table tbody tr:hover {
        background: #fafafa;
    }

    /* ----- Rank Badge ----- */
    .rank-badge {
        font-weight: 700;
        font-size: 13px;
        color: #b0b9c8;
    }

    .rank-badge.rank--gold   { color: #eab308; }
    .rank-badge.rank--silver { color: #94a3b8; }
    .rank-badge.rank--bronze { color: #b45309; }

    /* ----- Member Cell ----- */
    .member-name {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
    }

    .member-pos {
        font-size: 11px;
        color: #98a2b3;
        margin-top: 2px;
    }

    /* ----- Score Cell ----- */
    .score-cell {
        font-weight: 500;
        color: #374151;
    }

    /* ----- KPI Progress ----- */
    .kpi-pct {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #2563eb;
        margin-bottom: 5px;
    }

    .progress-bar-wrap {
        height: 6px;
        border-radius: 999px;
        background: #e5e7eb;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: #2563eb;
        transition: width 0.4s ease;
    }

    /* ----- Status Badges ----- */
    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge--success { background: #dcfce7; color: #15803d; }
    .badge--primary { background: #dbeafe; color: #1d4ed8; }
    .badge--warning { background: #fef3c7; color: #b45309; }
    .badge--danger  { background: #fee2e2; color: #dc2626; }

    /* ----- Modal ----- */
    .custom-modal {
        border-radius: 20px;
        border: none;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    }

    .btn-save {
        height: 40px;
        padding: 0 20px;
        border: none;
        border-radius: 12px;
        background: #2563eb;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
    }

    .btn-save:hover {
        background: #1d4ed8;
    }

.trend-card{
    background:#fff;
    border-radius:20px;
    padding:24px;
    margin-bottom:20px;
    border:1px solid #edf0f5;
    box-shadow:0 2px 12px rgba(0,0,0,.04);
}

.trend-header{
    margin-bottom:20px;
}

.trend-header h5{
    margin:0;
    font-size:18px;
    font-weight:700;
}

.trend-header span{
    font-size:12px;
    color:#98a2b3;
}

#kpiTrendChart{
    height:200px !important;
}
/* ===========================
   DASHBOARD TAB
=========================== */

.dashboard-tabs{
    display:flex;
    gap:10px;
    margin-bottom:20px;
}

.tab-btn{
    border:none;
    background:#fff;
    padding:10px 18px;
    border-radius:12px;
    font-size:14px;
    font-weight:600;
    color:#64748b;
    border:1px solid #e5e7eb;
    cursor:pointer;
    transition:.2s;
}

.tab-btn:hover{
    background:#f8fafc;
}

.tab-btn.active{
    background:#2563eb;
    color:#fff;
    border-color:#2563eb;
}

.tab-content{
    display:none;
}

.tab-content.active{
    display:block;
}
.bell-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 999px;
    background: #ef4444;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
</style>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

function showTab(tab) {

    document.querySelectorAll('.tab-content')
        .forEach(el => el.classList.remove('active'));

    document.querySelectorAll('.tab-btn')
        .forEach(el => el.classList.remove('active'));

    document
        .getElementById(tab + '-tab')
        .classList.add('active');

    document
        .querySelector(`[onclick="showTab('${tab}')"]`)
        .classList.add('active');
}

const ctx =
document.getElementById('kpiTrendChart');

new Chart(ctx, {

    type: 'line',

    data: {

        labels:
            @json($kpiTrendLabels),

        datasets: [{

            label:
                'Average KPI',

            data:
                @json($kpiTrendData),

            borderColor:
                '#2563eb',

            backgroundColor:
                'rgba(37,99,235,.1)',

            fill: true,

            tension: .4,

            borderWidth: 3,

            pointRadius: 5

        }]
    },

    options: {

        responsive: true,

        plugins: {

            legend: {
                display: false
            }
        },

        scales: {

            y: {

                min: 0,
                max: 100,

                ticks: {

                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});

</script>

@endsection