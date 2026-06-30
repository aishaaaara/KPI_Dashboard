@extends('admin.layouts.app')

@section('content')

<div class="container-fluid py-4">



    {{-- HEADER --}}
    <div class="communication-header">

        <div>
            <h2>Workload</h2>
            <p>
                Workload Management
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

            {{-- <button class="btn-add-data"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#addWorkloadModal">

                <i class="bi bi-plus-circle"></i>
                Add Data

            </button> --}}

        </div>

    </div>

{{-- PERIOD FILTER --}}
<div class="comm-period period-container">

    <button class="period-nav" onclick="scrollPeriod(-1)">
        <i class="bi bi-chevron-left"></i>
    </button>

    <div class="period-wrapper" id="periodWrapper">
        @foreach($periods as $period)
            @php
                $totalData = $workloadCounts[$period->id] ?? 0;
            @endphp

            <div class="period-card {{ $selectedPeriod == $period->id ? 'active-period' : '' }}"
                 id="period-{{ $period->id }}">

                <a href="?period_id={{ $period->id }}" class="period-link">
                    <i class="bi bi-calendar3"></i>
                    <span class="period-month">{{ substr($period->month, 0, 3) }} {{ $period->year }}</span>
                </a>

                <span class="period-total">{{ $totalData }}</span>

             
                <button class="btn-delete-period" type="button"
                        onclick="confirmDeletePeriod({{ $period->id }}, '{{ substr($period->month, 0, 3) }} {{ $period->year }}')">
                    <i class="bi bi-trash3"></i>
                </button>
                <form id="deletePeriodForm-{{ $period->id }}"
                    action="{{ route('workload.period.destroy', $period->id) }}"
                    method="POST"
                    style="display:none">
                    @csrf
                    @method('DELETE')
                </form>

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

                <th width="80">EID</th>
                <th width="220">Member</th>
                <th width="60">All</th>
                <th width="60">Todo</th>
                <th width="70">Progress</th>
                <th width="60">Review</th>
                <th width="60">Done</th>
                <th width="160">Completion</th>
                <th width="110">Status</th>
                <th width="90">Action</th>

                </tr>

                </thead>
                <tbody>

                    @forelse($workloads as $workload)

                    <tr>
                        @php
                        $completion =
                            $workload->all_task > 0
                            ? round(
                                ($workload->done / $workload->all_task) * 100 ) : 0;
                        @endphp

                        <td>

                            <div class="eid-box">

                                {{ $workload->member->eid }}

                            </div>

                        </td>

                        <td>

                            <div class="member-box">
                                <div>

                                    <div class="member-name">

                                        {{ $workload->member->name }}

                                    </div>

                                    <small class="member-label">

                                        {{ $workload->member->position->name }}

                                    </small>

                                </div>

                            </div>

                        </td>   

                        <td>
                            {{ $workload->all_task }}
                        </td>

                        <td>
                            {{ $workload->todo }}
                        </td>

                        <td>
                            {{ $workload->progress }}
                        </td>

                        <td>
                            {{ $workload->review }}
                        </td>

                        <td>
                            {{ $workload->done }}
                        </td>

                        <td>
                        <div class="achievement-wrapper">

                            <div class="progress achievement-progress">

                                <div class="progress-bar"
                                    style="width: {{ min($completion,100) }}%">
                                </div>

                            </div>

                            <span> {{ $completion }}%</span>

                        </div>
                        </td>
                        @php
                            if ($completion >= 100) {
                                $statusLabel = 'Done';
                                $statusClass = 'status-success';
                            } elseif ($completion >= 60) {
                                $statusLabel = 'Good';
                                $statusClass = 'status-primary';
                            } elseif ($completion >= 30) {
                                $statusLabel = 'At Risk';
                                $statusClass = 'status-warning';
                            } else {
                                $statusLabel = 'Critical';
                                $statusClass = 'status-danger';
                            }
                        @endphp

                        <td>
                            <span class="status-badge {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        @php
                            $periodDate = \Carbon\Carbon::createFromDate(
                                $workload->period->year,
                                date('n', strtotime($workload->period->month)),
                                1
                            );
                            $isLocked = $periodDate->lt(now()->startOfMonth());
                        @endphp

                        <td>
                            <div class="action-group">

                                @if (!$isLocked)
                                    <button type="button" class="btn-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $workload->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                @else
                                    <button class="btn-locked" disabled title="Periode terkunci">
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                @endif
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

                                    No Workload Data

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

@foreach($workloads as $workload)

<div class="modal fade"
     id="editModal{{ $workload->id }}"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content custom-modal">

            <form action="{{ route('admin.workload.update',$workload->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="modal-header border-0">

                    <h4 class="fw-bold">
                        Edit Workload
                    </h4>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                All Task
                            </label>

                            <input type="number"
                                   name="all_task"
                                   class="form-control custom-input"
                                   value="{{ $workload->all_task }}"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Todo
                            </label>

                            <input type="number"
                                   name="todo"
                                   class="form-control custom-input"
                                   value="{{ $workload->todo }}"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Progress
                            </label>

                            <input type="number"
                                   name="progress"
                                   class="form-control custom-input"
                                   value="{{ $workload->progress }}"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Review
                            </label>

                            <input type="number"
                                   name="review"
                                   class="form-control custom-input"
                                   value="{{ $workload->review }}"
                                   required>

                        </div>

                        <div class="col-md-12 mb-3">

                            <label class="form-label">
                                Done
                            </label>

                            <input type="number"
                                   name="done"
                                   class="form-control custom-input"
                                   value="{{ $workload->done }}"
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

                        Update Workload

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endforeach

{{-- MODAL ADD MONTH --}}
    <div class="modal fade" id="addPeriodModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal">

                <form action="{{ route('workload.period.store') }}" method="POST">
                    @csrf

                    <div class="modal-header border-0">
                        <h4 class="fw-bold">Add New Month</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Month</label>
                            <select id="monthSelect" name="month" class="form-select custom-input" required></select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <select id="yearSelect" name="year" class="form-select custom-input" required>
                                @for($year = now()->year; $year <= now()->year + 5; $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-save">Save Month</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

{{-- MODAL ADD WORKLOAD --}}
<div class="modal fade"
     id="addWorkloadModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content custom-modal">

            <form action="{{ route('admin.workload.store') }}"
                  method="POST">

                @csrf

                <div class="modal-header border-0">

                    <div>

                        <h4 class="fw-bold">
                            Add Workload
                        </h4>

                        <small class="text-muted">
                            Input workload data
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

                            <select
                                name="member_id"
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

                            <select
                                name="period_id"
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

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                All Task
                            </label>

                            <input type="number"
                                   name="all_task"
                                   class="form-control custom-input"
                                   required>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Todo
                            </label>

                            <input type="number"
                                   name="todo"
                                   class="form-control custom-input"
                                   required>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Progress
                            </label>

                            <input type="number"
                                   name="progress"
                                   class="form-control custom-input"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Review
                            </label>

                            <input type="number"
                                   name="review"
                                   class="form-control custom-input"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Done
                            </label>

                            <input type="number"
                                   name="done"
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

                        Save Workload

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- MODAL DELETE PERIOD --}}
<div class="modal fade" id="deletePeriodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content" style="border:none; border-radius:24px; overflow:hidden;">
            <div class="modal-body text-center" style="padding:40px 32px 32px">

                <div class="delete-period-icon-wrap">
                    <i class="bi bi-calendar-x-fill"></i>
                </div>

                <h5 class="delete-title">Delete Period</h5>

                <p class="delete-msg">
                    Are you sure you want to delete period
                    <strong id="deletePeriodLabel"></strong>?
                    <br>
                    <span style="color:#ef4444; font-size:12px;">
                        All workload data in this period will also be deleted.
                    </span>
                </p>

                <div class="delete-actions">
                    <button type="button" class="btn-del-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-del-confirm" id="btnConfirmDeletePeriod">
                        <i class="bi bi-trash3"></i>
                        Yes, Delete
                    </button>
                </div>

            </div>
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

function scrollPeriod(direction) {
    const wrapper = document.getElementById('periodWrapper');
    wrapper.scrollBy({ left: direction * 200, behavior: 'smooth' });
}

document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('periodWrapper');
    const active  = document.querySelector('.active-period');

    if (!wrapper) return;

    if (active) {
        // Scroll ke period yang aktif
        wrapper.scrollLeft = active.offsetLeft - (wrapper.offsetWidth / 2) + (active.offsetWidth / 2);
    } else {
        // Kalau tidak ada yang aktif, scroll ke paling kanan (terbaru)
        wrapper.scrollLeft = wrapper.scrollWidth;
    }
});

let deletePeriodTargetId = null;

function confirmDeletePeriod(id, label) {
    deletePeriodTargetId = id;
    document.getElementById('deletePeriodLabel').textContent = label;
    new bootstrap.Modal(document.getElementById('deletePeriodModal')).show();
}

document.getElementById('btnConfirmDeletePeriod').addEventListener('click', function () {
    if (deletePeriodTargetId) {
        document.getElementById('deletePeriodForm-' + deletePeriodTargetId).submit();
    }
});
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

.comm-period.period-container {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 24px;
    max-width: 100%;
    overflow: hidden;
}

.comm-period .period-wrapper {
    display: flex;
    gap: 6px;
    overflow-x: hidden;
    flex: 1;
    scroll-behavior: smooth;
    min-width: 0;
}

.comm-period .period-card {
    display: flex;
    flex-direction: row;
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

.comm-period .period-card:hover {
    border-color: #2563eb;
    background: #f0f6ff;
}

.comm-period .active-period {
    background: #2563eb !important;
    border-color: #2563eb !important;
}

.comm-period .active-period .period-month,
.comm-period .active-period .period-total,
.comm-period .active-period i {
    color: white !important;
}

.comm-period .period-link {
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
}

.comm-period .period-link i {
    font-size: 13px;
    color: #6b7280;
}

.comm-period .period-month {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
}

.comm-period .period-total {
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    background: #f3f4f6;
    border-radius: 999px;
    padding: 1px 7px;
    min-width: 20px;
    text-align: center;
}

.comm-period .active-period .period-total {
    background: rgba(255,255,255,.25);
    color: white;
}

.comm-period .btn-delete-period {
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
    width: auto;
    height: auto;
    border-radius: 0;
}

.comm-period .btn-delete-period:hover {
    color: #ef4444;
}

.comm-period .period-nav {
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

.comm-period .period-nav:hover {
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
.btn-locked {
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 12px;
    background: #f3f4f6;
    color: #9ca3af;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: not-allowed;
}
.delete-period-icon-wrap {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #fef2f2;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    border: 6px solid #fee2e2;
}
.delete-period-icon-wrap i { font-size: 30px; color: #ef4444; }

.delete-title  { font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 10px; }
.delete-msg    { font-size: 13px; color: #6b7280; line-height: 1.7; margin-bottom: 28px; }
.delete-actions { display: flex; gap: 10px; justify-content: center; }

.btn-del-cancel {
    height: 42px; padding: 0 24px;
    border: 1px solid #e5e7eb; border-radius: 12px;
    background: #fff; color: #374151;
    font-size: 13px; font-weight: 600;
    cursor: pointer; transition: background .15s;
}
.btn-del-cancel:hover { background: #f3f4f6; }

.btn-del-confirm {
    height: 42px; padding: 0 24px;
    border: none; border-radius: 12px;
    background: #ef4444; color: #fff;
    font-size: 13px; font-weight: 600;
    cursor: pointer;
    display: flex; align-items: center; gap: 7px;
    transition: background .15s;
}
.btn-del-confirm:hover { background: #dc2626; }
</style>

@endsection