@extends('admin.layouts.app')

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
            <h2>Story Points</h2>
            <p>
                Story Point Management
            </p>
        </div>

        <div class="header-action">

            <button class="btn-add-month"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#addPeriodModal">

                <i class="bi bi-calendar-plus"></i>
                Add Month

            </button>

            <button class="btn-add-data"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#addStoryPointModal">

                <i class="bi bi-plus-circle"></i>
                Add Data

            </button>

        </div>

    </div>

    {{-- PERIOD FILTER --}}
    <div class="period-container">

        <div class="period-wrapper">

            @foreach($periods as $period)

                @php

                $totalData = $allStoryPoints->where('period_id', $period->id)->count();

                @endphp

                <div class="period-card
                    {{ $selectedPeriod == $period->id ? 'active-period' : '' }}">

                    <a href="?period_id={{ $period->id }}"
                       class="period-link">

                        <div class="period-left">

                            <div class="period-icon">
                                <i class="bi bi-calendar-event"></i>
                            </div>

                            <div>

                                <span class="period-month">

                                    {{ substr($period->month,0,3) }}
                                    {{ $period->year }}

                                </span>

                                <small class="period-subtitle">

                                    Story Point Period
                                </small>

                            </div>

                        </div>

                    </a>

                    <div class="period-right">

                        <span class="period-total">

                            {{ $totalData }}

                        </span>

                        <form action="{{ route('story-points.period.destroy', $period->id) }}"
                              method="POST">

                            @csrf
                            @method('DELETE')

                            <button class="btn-delete-period"
                                    type="submit"
                                    onclick="return confirm('Delete this month and all story point data?')">

                                <i class="bi bi-trash3"></i>

                            </button>

                        </form>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

    {{-- TABLE --}}
    <div class="table-section">

        <div class="table-responsive">

            <table class="custom-table">

                <thead>

                     <tr>

                    <th width="90">EID</th>
                    <th>Member</th>
                    <th width="115">Target</th>
                    <th width="115">Total</th>
                    <th width="140">Achievement</th>
                    <th width="160">Status</th>
                    <th width="100" class="text-center">Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($storyPoints as $storyPoint)

                    <tr>
                    @php

                    $achievement =
                        $storyPoint->target > 0
                        ? round(($storyPoint->totals / $storyPoint->target) * 100)
                        : 0;

                    $status =
                        $achievement >= 110 ? 'Exceeded' :
                        ($achievement >= 90 ? 'Achieved' :
                        ($achievement >= 75 ? 'Near Target' :
                        'Below Target'));

                    $statusClass =
                        $achievement >= 110 ? 'status-success' :
                        ($achievement >= 90 ? 'status-primary' :
                        ($achievement >= 75 ? 'status-warning' :
                        'status-danger'));

                    @endphp
                                            <td>

                            <div class="eid-box">

                                {{ $storyPoint->member->eid }}

                            </div>

                        </td>

                        <td>

                            <div class="member-box">
                                <div>

                                    <div class="member-name">

                                        {{ $storyPoint->member->name }}

                                    </div>

                                    <small class="member-label">

                                        {{ $storyPoint->member->position->name }}

                                    </small>

                                </div>

                            </div>

                        </td>   

                        <td>

    <span class="point-number">

        {{ number_format($storyPoint->target) }}

    </span>

</td>
<td>

    <span class="point-number total-point">

        {{ number_format($storyPoint->totals) }}

    </span>

</td>

                       <td>

    <div class="achievement-wrapper">

        <div class="progress achievement-progress">

            <div
                class="progress-bar"
                style="width: {{ min($achievement,100) }}%">
            </div>

        </div>

        <span>

            {{ $storyPoint->summary }} %
        </span>

    </div>

</td>

<td>

    <span class="status-badge {{ $statusClass }}">
        {{ $status }}
    </span>

</td>

<td>

    <div class="action-group">

        <button
            type="button"
            class="btn-edit"
            data-bs-toggle="modal"
            data-bs-target="#editModal{{ $storyPoint->id }}">

            <i class="bi bi-pencil-square"></i>

        </button>

        <form action="{{ route('story-points.destroy',$storyPoint->id) }}"
              method="POST"
              onsubmit="return confirm('Delete this story point?')">

            @csrf
            @method('DELETE')

            <button type="submit"
                    class="btn-delete">

                <i class="bi bi-trash"></i>

            </button>

        </form>

    </div>

</td>

</td>
                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="7"
                            class="empty-data">

                            <div class="empty-wrapper">

                                <i class="bi bi-folder-x"></i>

                                <p>

                                    No Story Point Data

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

@foreach($storyPoints as $storyPoint)

<div class="modal fade"
     id="editModal{{ $storyPoint->id }}"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content custom-modal">

            <form action="{{ route('story-points.update',$storyPoint->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="modal-header border-0">

                    <h4 class="fw-bold mb-0">
                        Edit Story Point
                    </h4>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Target
                        </label>

                        <input type="number"
                               name="target"
                               class="form-control custom-input"
                               value="{{ $storyPoint->target }}"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Total
                        </label>

                        <input type="number"
                               name="totals"
                               class="form-control custom-input"
                               value="{{ $storyPoint->totals }}"
                               required>

                    </div>

                    

                </div>

                <div class="modal-footer border-0">

                    <button type="button"
                            class="btn-cancel"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn-save">

                        Update Story Point

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endforeach

{{-- MODAL ADD MONTH --}}
<div class="modal fade"
     id="addPeriodModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content custom-modal">

<form action="{{ route('communication.period.store') }}"
      method="POST">

    @csrf

    {{-- MONTH --}}
    <div class="mb-3">

        <label class="form-label">
            Month
        </label>

        <select
            id="monthSelect"
            name="month"
            class="form-select"
            required>

        </select>

    </div>

    {{-- YEAR --}}
    <div class="mb-3">

        <label class="form-label">
            Year
        </label>

        <select
            id="yearSelect"
            name="year"
            class="form-select"
            required>

            @for(
                $year = now()->year;
                $year <= now()->year + 5;
                $year++
            )

                <option value="{{ $year }}">
                    {{ $year }}
                </option>

            @endfor

        </select>

    </div>

    <button type="submit"
            class="btn btn-primary">
        Save
    </button>

</form>

        </div>

    </div>

</div>

{{-- MODAL ADD STORY POINT --}}
<div class="modal fade"
     id="addStoryPointModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content custom-modal">

            <form action="{{ route('story-points.store') }}"
                  method="POST">

                @csrf

                <div class="modal-header border-0">

                    <div>

                        <h4 class="fw-bold">

                            Add Story Point

                        </h4>

                        <small class="text-muted">

                            Input story point data

                        </small>

                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Member

                            </label>

                            <select name="member_id"
                                    class="form-select custom-input"
                                    required>

                                <option value="">

                                    Select Member

                                </option>

                                @foreach($members as $member)

                                    <option value="{{ $member->id }}">

                                        {{ $member->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Period

                            </label>

                            <select name="period_id"
                                    class="form-select custom-input"
                                    required>

                                <option value="">

                                    Select Period

                                </option>

                                @foreach($periods as $period)

                                    <option value="{{ $period->id }}">

                                        {{ $period->month }}
                                        {{ $period->year }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Target

                            </label>

                            <input type="number"
                                   name="target"
                                   class="form-control custom-input"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Total

                            </label>

                            <input type="number"
                                   name="totals"
                                   class="form-control custom-input"
                                   required>

                        </div>



                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button type="button"
                            class="btn-cancel"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn-save">

                        Save Story Point

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- SCRIPT --}}
<script>

const currentYear =
    {{ now()->year }};

const currentMonth =
    {{ now()->month }};

const months = [

    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'

];

const existingPeriods =
@json(
    $existingPeriods->map(function($period){

        return [

            'month' => $period->month,

            'year' => $period->year

        ];

    })
);

function loadMonths()
{
    const selectedYear =
        parseInt(
            document
                .getElementById('yearSelect')
                .value
        );

    const monthSelect =
        document.getElementById(
            'monthSelect'
        );

    monthSelect.innerHTML =
        '<option value="">Select Month</option>';

    months.forEach((month,index)=>{

        const monthNumber =
            index + 1;

        const isPastMonth =

            selectedYear === currentYear
            &&
            monthNumber < currentMonth;

        const alreadyExists =
            existingPeriods.some(period =>

                period.month === month
                &&
                parseInt(period.year)
                    === selectedYear

            );

        if(
            !isPastMonth
            &&
            !alreadyExists
        ){

            monthSelect.innerHTML +=
                `
                <option value="${month}">
                    ${month}
                </option>
                `;

        }

    });

}

document
    .getElementById('yearSelect')
    .addEventListener(
        'change',
        loadMonths
    );

loadMonths();

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
}

.btn-add-month:hover,
.btn-add-data:hover{
    background:#2388f5;
}

.period-container{
    margin-bottom:24px;
}

.period-wrapper {
    display: flex;
    flex-direction: row-reverse;  /* ← tambah ini */
    gap: 14px;
    overflow-x: auto;
    padding-bottom: 4px;
    justify-content: flex-end;   /* ← supaya mulai dari kiri */
}
.period-card{
    min-width:210px;
    background:white;
    border-radius:20px;
    border:1px solid #edf0f5;
    padding:14px 16px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.period-link{
    text-decoration:none;
    flex:1;
}

.period-left{
    display:flex;
    align-items:center;
    gap:12px;
}

.period-icon{
    width:42px;
    height:42px;
    border-radius:14px;
    background:#f4f7fb;
    display:flex;
    align-items:center;
    justify-content:center;
}

.period-month{
    display:block;
    color:#374151;
    font-size:14px;
    font-weight:700;
}

.period-subtitle{
    color:#98a2b3;
    font-size:11px;
}

.period-right{
    display:flex;
    align-items:center;
    gap:8px;
}

.period-total{
    width:28px;
    height:28px;
    border-radius:999px;
    background:#f3f4f6;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:11px;
    font-weight:700;
    color:#6b7280;
}

.active-period{
    background:#3b82f6;
    border-color:#3b82f6;
}

.active-period .period-month,
.active-period .period-subtitle,
.active-period i{
    color:white !important;
}

.active-period .period-icon{
    background:rgba(255,255,255,0.15);
}

.active-period .period-total{
    background:rgba(255,255,255,0.2);
    color:white;
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
    border-collapse:collapse;
}

.custom-table thead tr{
    background:#f7f8fc;
}

.custom-table th{
    padding:16px;
    color:#98a2b3;
    font-size:12px;
    font-weight:700;
    text-align:left;
}

.custom-table tbody tr{
    border-bottom:1px solid #f1f1f1;
}

.custom-table tbody tr:hover{
    background:#fafcff;
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
    align-items:center;
    justify-content:space-between;
    gap:10px;
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
.period-card{
    transition:all .25s ease;
}

.period-card:hover{
    transform:translateY(-3px);
    box-shadow:0 12px 25px rgba(59,130,246,.12);
}

.btn-add-month,
.btn-add-data,
.btn-save,
.btn-edit,
.btn-delete{
    transition:.25s;
}

.btn-add-month:hover,
.btn-add-data:hover{
    transform:translateY(-2px);
}

.btn-edit:hover{
    background:#dbeafe;
}

.btn-delete:hover{
    background:#fee2e2;
}

.btn-save:hover{
    background:#2563eb;
}

.overall-card{
    min-width:90px;
}

.table-responsive::-webkit-scrollbar{
    height:8px;
}

.table-responsive::-webkit-scrollbar-thumb{
    background:#d1d5db;
    border-radius:999px;
}

.custom-modal{
    box-shadow:0 20px 60px rgba(0,0,0,.15);
}

@media(max-width:768px){

    .communication-header{
        flex-direction:column;
        align-items:flex-start;
    }

    .header-action{
        width:100%;
    }

    .btn-add-month,
    .btn-add-data{
        flex:1;
        justify-content:center;
    }

    .period-card{
        min-width:180px;
    }

}
.eid-badge{
    background:#eff6ff;
    color:#2563eb;
    padding:7px 12px;
    border-radius:10px;
    font-size:12px;
    font-weight:700;
}

.point-number{
    font-size:15px;
    font-weight:700;
    color:#374151;
}

.total-point{
    color:#2563eb;
}

.achievement-wrapper{
    min-width:120px;
}

.achievement-progress{
    height:8px;
    margin-bottom:5px;
    border-radius:999px;
    background:#e5e7eb;
}

.achievement-progress .progress-bar{
    background:#3b82f6;
}

.status-badge{
    padding:6px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
    display:inline-block;
}

.status-success{
    background:#dcfce7;
    color:#15803d;
}

.status-primary{
    background:#dbeafe;
    color:#1d4ed8;
}

.status-warning{
    background:#fef3c7;
    color:#b45309;
}

.status-danger{
    background:#fee2e2;
    color:#dc2626;
}

.custom-table tbody tr{
    height:72px;
}
.action-group{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
}

.action-group form{
    margin:0;
}

.custom-table td:last-child{
    text-align:center;
}
</style>

@endsection