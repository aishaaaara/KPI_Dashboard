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
            <h2>Communication Matrix</h2>
            <p>
                Communication Management
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
                    data-bs-target="#addCommunicationModal">

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

                    $totalData =
                        $communications
                        ->where('period_id', $period->id)
                        ->count();

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

                                    Communication Period

                                </small>

                            </div>

                        </div>

                    </a>

                    <div class="period-right">

                        <span class="period-total">

                            {{ $totalData }}

                        </span>

                        <form action="{{ route('communication.period.destroy', $period->id) }}"
                              method="POST">

                            @csrf
                            @method('DELETE')

                            <button class="btn-delete-period"
                                    type="submit"
                                    onclick="return confirm('Delete this month and all communication data?')">

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

                        <th>EID</th>
                        <th>Member</th>
                        <th>Clarity</th>
                        <th>Responsiveness</th>
                        <th>Collaboration</th>
                        <th>Score</th>
                        <th class="text-center">Action</th>

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

                        {{-- ACTION --}}
                        <td>

                            <div class="action-group">

                                {{-- EDIT --}}
                                <button class="btn-edit"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $communication->id }}">

                                    <i class="bi bi-pencil-fill"></i>

                                </button>

                                {{-- DELETE --}}
                                <form action="{{ route('communication.destroy', $communication->id) }}"
                                      method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn-delete"
                                            type="submit"
                                            onclick="return confirm('Delete communication data?')">

                                        <i class="bi bi-trash-fill"></i>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    {{-- EDIT MODAL --}}
                    <div class="modal fade"
                         id="editModal{{ $communication->id }}"
                         tabindex="-1"
                         aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered modal-lg">

                            <div class="modal-content custom-modal">

                                <form action="{{ route('communication.update', $communication->id) }}"
                                      method="POST">

                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header border-0">

                                        <div>

                                            <h4 class="fw-bold">

                                                Edit Communication

                                            </h4>

                                            <small class="text-muted">

                                                Update KPI communication data

                                            </small>

                                        </div>

                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                        </button>

                                    </div>

                                    <div class="modal-body">

                                        <div class="row">

                                            <div class="col-md-4 mb-3">

                                                <label class="form-label">
                                                    Clarity
                                                </label>

                                                <input type="number"
                                                       name="clarity"
                                                       class="form-control custom-input"
                                                       value="{{ $communication->clarity }}">

                                            </div>

                                            <div class="col-md-4 mb-3">

                                                <label class="form-label">
                                                    Responsiveness
                                                </label>

                                                <input type="number"
                                                       name="responsiveness"
                                                       class="form-control custom-input"
                                                       value="{{ $communication->responsiveness }}">

                                            </div>

                                            <div class="col-md-4 mb-3">

                                                <label class="form-label">
                                                    Collaboration
                                                </label>

                                                <input type="number"
                                                       name="collaboration"
                                                       class="form-control custom-input"
                                                       value="{{ $communication->collaboration }}">

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

                                            Update Data

                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

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

                <div class="modal-header border-0">

                    <div>

                        <h4 class="fw-bold">
                            Add New Month
                        </h4>

                        <small class="text-muted">
                            Select period for communication data
                        </small>

                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Select Month
                        </label>

                        <select name="period"
                                class="form-select custom-input"
                                id="periodSelect">

                            @php

                                $existingPeriods =
                                    $periods
                                    ->map(function($item){

                                        return
                                            strtolower($item->month)
                                            .
                                            '-'
                                            .
                                            $item->year;

                                    })
                                    ->toArray();

                            @endphp

                            @for($i = 0; $i < 6; $i++)

                                @php

                                    $date =
                                        now()->addMonths($i);

                                    $monthName =
                                        $date->format('F');

                                    $year =
                                        $date->format('Y');

                                    $periodKey =
                                        strtolower($monthName)
                                        .
                                        '-'
                                        .
                                        $year;

                                @endphp

                                @if(!in_array($periodKey, $existingPeriods))

                                    <option value="{{ $monthName }}|{{ $year }}">

                                        {{ $monthName }} {{ $year }}

                                    </option>

                                @endif

                            @endfor

                        </select>

                    </div>

                    <input type="hidden"
                           name="month"
                           id="monthInput">

                    <input type="hidden"
                           name="year"
                           id="yearInput">

                </div>

                <div class="modal-footer border-0">

                    <button type="button"
                            class="btn-cancel"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn-save">

                        Save Month

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- MODAL ADD COMMUNICATION --}}
<div class="modal fade"
     id="addCommunicationModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content custom-modal">

            <form action="{{ route('communication.store') }}"
                  method="POST">

                @csrf

                <div class="modal-header border-0">

                    <div>

                        <h4 class="fw-bold">
                            Add Communication Data
                        </h4>

                        <small class="text-muted">
                            Input communication KPI member
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

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Clarity (%)
                            </label>

                            <input type="number"
                                   name="clarity"
                                   class="form-control custom-input"
                                   required>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Responsiveness (%)
                            </label>

                            <input type="number"
                                   name="responsiveness"
                                   class="form-control custom-input"
                                   required>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Collaboration (%)
                            </label>

                            <input type="number"
                                   name="collaboration"
                                   class="form-control custom-input"
                                   required>

                        </div>

                        <div class="col-md-12">

                            <label class="form-label">
                                Notes
                            </label>

                            <textarea name="notes"
                                      rows="4"
                                      class="form-control custom-input textarea-custom"></textarea>

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

                        Save Communication

                    </button>

                </div>

            </form>

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

.period-container{
    margin-bottom:24px;
}

.period-wrapper{
    display:flex;
    gap:14px;
    overflow-x:auto;
    padding-bottom:4px;
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
    text-align:center;
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