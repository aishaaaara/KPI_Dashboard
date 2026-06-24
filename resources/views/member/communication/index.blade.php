@extends('member.layouts.app')

@section('content')

<div class="container-fluid py-4">

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert-success-custom">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error-custom">
            {{ session('error') }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="communication-header">

        <div>
            <h2>Communication Matrix</h2>
            <p>
                Communication Management
            </p>
        </div>
    </div>

{{-- PERIOD FILTER --}}
<div class="period-container">

    <button class="period-nav" onclick="scrollPeriod(-1)">
        <i class="bi bi-chevron-left"></i>
    </button>

    <div class="period-wrapper" id="periodWrapper">
        @foreach($periods as $period)
            @php
                $totalData = $communicationCounts[$period->id] ?? 0;
            @endphp

            <div class="period-card {{ $selectedPeriod == $period->id ? 'active-period' : '' }}"
                 id="period-{{ $period->id }}">

                <a href="?period_id={{ $period->id }}" class="period-link">
                    <i class="bi bi-calendar3"></i>
                    <span class="period-month">{{ substr($period->month, 0, 3) }} {{ $period->year }}</span>
                </a>

                <span class="period-total">{{ $totalData }}</span>
            </div>
        @endforeach
    </div>

    <button class="period-nav" onclick="scrollPeriod(1)">
        <i class="bi bi-chevron-right"></i>
    </button>

</div>

    {{-- TABLE --}}
    <div class="table-section">

        <div class="table-responsive">

            <table class="custom-table">

                <thead>

                    <tr>

                        <th>EID</th>
                        <th>Member</th>
                        <th>Clarity</th>
                        <th>Responsiveness</th>
                        <th>Collaboration</th>
                        <th>Score</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($communications as $communication)

                    <tr>

                        {{-- EID --}}
                        <td>

                            <div class="eid-box">

                                {{ $communication->member->eid }}

                            </div>

                        </td>

                        {{-- MEMBER --}}
                        <td>

                            <div class="member-box">
                                <div>

                                    <div class="member-name">

                                        {{ $communication->member->name }}

                                    </div>

                                    <small class="member-label">

                                        {{ $communication->member->position->name }}

                                    </small>

                                </div>

                            </div>

                        </td>

                        {{-- CLARITY --}}
                        <td>
                          <div class="score-box">
                                <div class="score-circle
                                    {{ $communication->clarity >= 90 ? 'green' : ($communication->clarity >= 80 ? 'yellow' : 'red') }}">
                                    {{ $communication->clarity }}%
                                </div>
                            </div>

                        </td>

                        {{-- RESPONSIVENESS --}}
                       <td>
                          <div class="score-box">
                                <div class="score-circle
                                    {{ $communication->responsiveness >= 90 ? 'green' : ($communication->responsiveness >= 80 ? 'yellow' : 'red') }}">
                                    {{ $communication->responsiveness }}%
                                </div>

                            </div>

                        </td>

                        {{-- COLLABORATION --}}
                       <td>
                          <div class="score-box">
                                <div class="score-circle
                                    {{ $communication->collaboration >= 90 ? 'green' : ($communication->collaboration >= 80 ? 'yellow' : 'red') }}">
                                    {{ $communication->collaboration }}%
                                </div>
                            </div>

                        </td>

                        {{-- SCORE --}}
                      <td class="text-center">

                <div class="score-badge
                    {{ $communication->overall_score >= 90 ? 'score-good' :
                    ($communication->overall_score >= 80 ? 'score-medium' : 'score-low') }}">

                    {{ number_format($communication->overall_score, 0) }}%

                </div>

            </td>

                    @empty

                    <tr>

                        <td colspan="7"
                            class="empty-data">

                            <div class="empty-wrapper">

                                <i class="bi bi-folder-x"></i>

                                <p>
                                    No communication data available
                                </p>

                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- SCRIPT --}}
<script>

    const periodSelect =
        document.getElementById('periodSelect');

    const monthInput =
        document.getElementById('monthInput');

    const yearInput =
        document.getElementById('yearInput');

    function setPeriodValue(){

        if(periodSelect){

            const value =
                periodSelect.value.split('|');

            monthInput.value =
                value[0];

            yearInput.value =
                value[1];
        }
    }

    setPeriodValue();

    if(periodSelect){

        periodSelect.addEventListener(
            'change',
            setPeriodValue
        );
    }

</script>

<style>

.communication-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:24px;
    gap:20px;
    flex-wrap:wrap;
}

.communication-header h2{
    font-size:24px;
    font-weight:700;
    color:#111827;
    margin:0;
}

.communication-header p{
    color:#98a2b3;
    font-size:14px;
    margin-top:4px;
    margin-bottom:0;
}

.header-action{
    display:flex;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
}

.btn-add-month,
.btn-add-data{
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

.btn-add-month:hover,
.btn-add-data:hover{
    background: #1d4ed8;
}

.period-container {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 24px;
    max-width: 100%;
    overflow: hidden;
}

.period-wrapper {
    display: flex;
    gap: 6px;
    overflow-x: hidden;
    flex: 1;
    scroll-behavior: smooth;
    min-width: 0; /* ← kunci utama, paksa flex child tidak overflow */
}
.period-card {
    display: flex;
    flex-direction: row;  /* ← horizontal */
    align-items: center;
    gap: 8px;
    padding: 7px 12px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    background: #fff;
    white-space: nowrap;
    flex-shrink: 0;
    transition: all .15s;
    cursor: pointer;
}
.period-card:hover {
    border-color: #2563eb;
    background: #f0f6ff;
}

.active-period {
    background: #2563eb !important;
    border-color: #2563eb !important;
}

.active-period .period-month,
.active-period .period-total,
.active-period i {
    color: white !important;
}

.period-link {
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
}

.period-link i {
    font-size: 13px;
    color: #6b7280;
}

.period-month {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
}

.period-total {
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    background: #f3f4f6;
    border-radius: 999px;
    padding: 1px 7px;
    min-width: 20px;
    text-align: center;
}

.active-period .period-total {
    background: rgba(255,255,255,.25);
}

.btn-delete-period {
    background: transparent;
    border: none;
    color: #d1d5db;
    font-size: 12px;
    padding: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: color .15s;
    line-height: 1;
}

.btn-delete-period:hover {
    color: #ef4444;
}

.period-nav {
    width: 30px;
    height: 30px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: all .15s;
    font-size: 12px;
}

.period-nav:hover {
    border-color: #2563eb;
    color: #2563eb;
    background: #f0f6ff;
}

.table-section{
    background:white;
    border-radius:24px;
    padding:24px;
    box-shadow:0 4px 18px rgba(0,0,0,0.03);
}

.table-responsive{
    overflow-x:auto;
}

.custom-table{
    width:100%;
    border-collapse:separate;
    border-spacing:0;
}

.custom-table thead th{
    padding:16px 20px;
    font-size:12px;
    font-weight:600;
    color:#64748b;
    background:#f8fafc;
}

.custom-table tbody td{
    padding:18px 20px;
    font-size:14px;
    color:#334155;
}

.custom-table tbody tr:hover{
    background:#f8fbff;
}

.custom-table td{
    padding:18px 16px;
    font-size:13px;
    color:#374151;
    vertical-align:middle;
}

.eid-box{
    font-weight:700;
}

.member-box{
    display:flex;
    align-items:center;
    gap:12px;
}

.member-avatar{
    width:40px;
    height:40px;
    border-radius:999px;
    background:#eef5ff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    color:#3498ff;
}

.member-name{
    font-weight:700;
}

.member-label{
    color:#98a2b3;
    font-size:11px;
}

.score-box{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:8px;
}

.score-circle{
    width:56px;
    height:56px;
    border-radius:50%;
    border:4px solid;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:16px;
    font-weight:700;
    background:#fff;
}

.score-label{
    font-size:11px;
    color:#98a2b3;
    text-align:center;
    font-weight:500;
}

.score-percent{
    font-weight:700;
}

.score-label{
    color:#98a2b3;
    font-size:10px;
}

.score-circle{
    width:34px;
    height:34px;
    border-radius:999px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:10px;
    font-weight:700;
    border:3px solid;
}

.green{
    color:#22c55e;
    border-color:#22c55e;
}

.yellow{
    color:#f59e0b;
    border-color:#f59e0b;
}

.red{
    color:#ef4444;
    border-color:#ef4444;
}

.overall-card{
    background:#f7fbff;
    border-radius:14px;
    padding:10px 12px;
    text-align:center;
}

.overall-score{
    color:#3b82f6;
    font-size:16px;
    font-weight:700;
}

.action-group{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
}

.btn-edit,
.btn-delete,
.btn-delete-period{
    width:36px;
    height:36px;
    border:none;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
}

.btn-edit{
    background:#eef6ff;
    color:#3498ff;
}

.btn-delete{
    background:#fff1f1;
    color:#ef4444;
}

.btn-delete-period{
    background:transparent;
    color:#ef4444;
}

.custom-modal{
    border:none;
    border-radius:24px;
    padding:10px;
}

.custom-input{
    border-radius:14px;
    border:1px solid #e5e7eb;
    height:50px;
    padding:0 14px;
}

.custom-input:focus{
    box-shadow:none;
    border-color:#3498ff;
}

.textarea-custom{
    height:auto;
    padding:14px;
}

.form-label{
    font-size:13px;
    font-weight:600;
    margin-bottom:8px;
}

.btn-save{
    background:#3498ff;
    color:white;
    border:none;
    padding:12px 22px;
    border-radius:14px;
    font-weight:600;
}

.btn-cancel{
    background:#f3f4f6;
    border:none;
    padding:12px 22px;
    border-radius:14px;
}

.alert-success-custom{
    background:#e8fff1;
    color:#16a34a;
    padding:14px 18px;
    border-radius:14px;
    margin-bottom:18px;
}

.alert-error-custom{
    background:#ffecec;
    color:#ef4444;
    padding:14px 18px;
    border-radius:14px;
    margin-bottom:18px;
}

.empty-data{
    text-align:center;
    padding:40px !important;
}

.empty-wrapper{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:10px;
    color:#98a2b3;
}

.empty-wrapper i{
    font-size:42px;
}
.score-badge{
    min-width:70px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:10px 14px;
    border-radius:14px;
    font-size:15px;
    font-weight:700;
}

.score-good{
    color:#16a34a;
}

.score-medium{
    color:#d97706;
}

.score-low{
    color:#dc2626;
}
</style>

@endsection