@extends('admin.layouts.app')

@section('content')

<div class="container-fluid py-4">

    @if(session('success'))

        <div class="alert-success-custom">

            {{ session('success') }}

        </div>

    @endif

    {{-- HEADER --}}
    <div class="member-header">

        <div>

            <h2>Team Member</h2>

            <p>
                Members Management
            </p>

        </div>

        <button class="btn-add"
                data-bs-toggle="modal"
                data-bs-target="#addMemberModal">

            + Add Member

        </button>

    </div>

    {{-- TABLE --}}
    <div class="table-section">

        <div class="table-responsive">

            <table class="custom-table">

                <thead>

                    <tr>

                        <th>Name</th>
                        <th>Position</th>
                        <th>Type</th>
                        <th>Team</th>
                        <th>Join Date</th>
                        <th>End Date</th>
                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($members as $member)

                    <tr>

                        {{-- NAME --}}
                        <td class="member-name">

                            {{ $member->name }}

                        </td>

                        {{-- POSITION --}}
                        <td>

                            {{ $member->position->name }}

                        </td>

                        {{-- TYPE --}}
                        <td>

                            @if(strtolower($member->employmentType->name) == 'full-time')

                                <span class="badge-full">

                                    {{ $member->employmentType->name }}

                                </span>

                            @else

                                <span class="badge-intern">

                                    {{ $member->employmentType->name }}

                                </span>

                            @endif

                        </td>

                        {{-- TEAM --}}
                        <td>

                            <span class="badge-team">

                                {{ $member->team->name }}

                            </span>

                        </td>

                        {{-- JOIN DATE --}}
                        <td>

                            {{ \Carbon\Carbon::parse($member->join_date)->format('d/m/y') }}

                        </td>

                        {{-- END DATE --}}
                        <td>

                            {{ \Carbon\Carbon::parse($member->end_date)->format('d/m/y') }}

                        </td>

                        {{-- ACTION --}}
                        <td>

                            <div class="action-group">

                                {{-- EDIT --}}
                                <button class="btn-action btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $member->id }}">

                                    <i class="bi bi-pencil-fill"></i>

                                </button>

                                {{-- DELETE --}}
                                <form action="{{ route('members.destroy', $member->id) }}"
                                      method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn-action btn-delete"
                                            onclick="return confirm('Yakin hapus data?')">

                                        <i class="bi bi-trash-fill"></i>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    {{-- EDIT MODAL --}}
                    <div class="modal fade"
                         id="editModal{{ $member->id }}"
                         tabindex="-1">

                        <div class="modal-dialog modal-dialog-centered modal-lg">

                            <div class="modal-content custom-modal">

                                <div class="modal-header border-0">

                                    <div>

                                        <h4 class="fw-bold">
                                            Edit Employee
                                        </h4>

                                        <small class="text-muted">
                                            Update employee data
                                        </small>

                                    </div>

                                    <button type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal">
                                    </button>

                                </div>

                                <form action="{{ route('members.update', $member->id) }}"
                                      method="POST">

                                    @csrf
                                    @method('PUT')

                                    <div class="modal-body">

                                        <div class="row">

                                            {{-- NAME --}}
                                            <div class="col-md-6 mb-3">

                                                <label>Name *</label>

                                                <input type="text"
                                                       name="name"
                                                       value="{{ $member->name }}"
                                                       class="form-control custom-input">

                                            </div>

                                            {{-- POSITION --}}
                                            <div class="col-md-6 mb-3">

                                                <label>Position *</label>

                                                <select name="position_id"
                                                        class="form-select custom-input">

                                                    @foreach($positions as $position)

                                                        <option value="{{ $position->id }}"
                                                            {{ $member->position_id == $position->id ? 'selected' : '' }}>

                                                            {{ $position->name }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>

                                            {{-- TEAM --}}
                                            <div class="col-md-6 mb-3">

                                                <label>Team *</label>

                                                <select name="team_id"
                                                        class="form-select custom-input">

                                                    @foreach($teams as $team)

                                                        <option value="{{ $team->id }}"
                                                            {{ $member->team_id == $team->id ? 'selected' : '' }}>

                                                            {{ $team->name }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>

                                            {{-- TYPE --}}
                                            <div class="col-md-6 mb-3">

                                                <label>Type *</label>

                                                <select name="employment_type_id"
                                                        class="form-select custom-input">

                                                    @foreach($employmentTypes as $type)

                                                        <option value="{{ $type->id }}"
                                                            {{ $member->employment_type_id == $type->id ? 'selected' : '' }}>

                                                            {{ $type->name }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>

                                            {{-- JOIN DATE --}}
                                            <div class="col-md-6 mb-3">

                                                <label>Join Date</label>

                                                <input type="date"
                                                       name="join_date"
                                                       value="{{ $member->join_date }}"
                                                       class="form-control custom-input">

                                            </div>

                                            {{-- END DATE --}}
                                            <div class="col-md-6 mb-3">

                                                <label>End Date</label>

                                                <input type="date"
                                                       name="end_date"
                                                       value="{{ $member->end_date }}"
                                                       class="form-control custom-input">

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

                                            Update

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

                            No Data

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- ADD MEMBER MODAL --}}
<div class="modal fade"
     id="addMemberModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content custom-modal">

            <div class="modal-header border-0">

                <div>

                    <h4 class="fw-bold">
                        Add Employee
                    </h4>

                    <small class="text-muted">
                        Create new employee data
                    </small>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <form action="{{ route('members.store') }}"
                  method="POST">

                @csrf

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label>Name *</label>

                            <input type="text"
                                   name="name"
                                   class="form-control custom-input"
                                   required
                                   placeholder="Enter Full Name">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Position *</label>

                            <select name="position_id"
                                    class="form-select custom-input"
                                    required>

                                @foreach($positions as $position)

                                    <option value="{{ $position->id }}">

                                        {{ $position->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Team *</label>

                            <select name="team_id"
                                    class="form-select custom-input"
                                    required>

                                @foreach($teams as $team)

                                    <option value="{{ $team->id }}">

                                        {{ $team->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Type *</label>

                            <select name="employment_type_id"
                                    class="form-select custom-input"
                                    required>

                                @foreach($employmentTypes as $type)

                                    <option value="{{ $type->id }}">

                                        {{ $type->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Join Date</label>

                            <input type="date"
                                   name="join_date"
                                   class="form-control custom-input">

                        </div>
                         <div class="col-md-6 mb-3">

                            <label>End Date</label>

                            <input type="date"
                                   name="end_date"
                                   class="form-control custom-input">

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

                        Save Member

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<style>

body{
    background:#f5f6fa;
    overflow-x:hidden;
}

/* HEADER */
.member-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:24px;
}

.member-header h2{
    font-size:24px;
    font-weight:700;
    color:#111827;
    margin:0;
}

.member-header p{
    color:#98a2b3;
    font-size:13px;
    margin-top:4px;
    margin-bottom:0;
}

/* BUTTON */
.btn-add{
    background:#3498ff;
    color:white;
    border:none;
    padding:11px 18px;
    border-radius:14px;
    font-weight:600;
    font-size:14px;
    transition:0.2s;
}

.btn-add:hover{
    background:#2388f5;
}

/* TABLE CARD */
.table-section{
    background:white;
    border-radius:24px;
    padding:24px;
    width:100%;
    box-shadow:0 4px 18px rgba(0,0,0,0.03);
}

.table-title{
    font-size:18px;
    font-weight:700;
    margin-bottom:20px;
    color:#111827;
}

.table-responsive{
    width:100%;
    overflow-x:auto;
}

/* TABLE */
.custom-table{
    width:100%;
    min-width:850px;
    border-collapse:collapse;
}

.custom-table thead tr{
    background:#f7f8fc;
}

.custom-table thead th{
    padding:16px;
    font-size:12px;
    font-weight:600;
    color:#98a2b3;
    text-align:left;
}

.custom-table tbody tr{
    border-bottom:1px solid #f1f1f1;
    transition:0.2s;
}

.custom-table tbody tr:hover{
    background:#fafcff;
}

.custom-table tbody td{
    padding:18px 16px;
    font-size:13px;
    color:#374151;
    vertical-align:middle;
}

.member-name{
    min-width:160px;
    font-weight:600;
}

.badge-full,
.badge-intern,
.badge-team{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:7px 14px;
    border-radius:999px;
    font-size:11px;
    font-weight:600;
    white-space:nowrap;
}

.badge-full{
    background:#dcfce7;
    color:#16a34a;
}

.badge-intern{
    background:#fef3c7;
    color:#f59e0b;
}

.badge-team{
    background:#edf4ff;
    color:#3b82f6;
}

/* ACTION */
.action-group{
    display:flex;
    align-items:center;
    gap:8px;
}

.btn-action{
    width:36px;
    height:36px;
    border:none;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    transition:0.2s;
}

.btn-edit{
    background:#eef4ff;
    color:#3b82f6;
}

.btn-delete{
    background:#ffecec;
    color:#ef4444;
}

.btn-action:hover{
    transform:translateY(-2px);
}

/* EMPTY */
.empty-data{
    text-align:center;
    padding:30px !important;
    color:#999;
}

/* MODAL */
.custom-modal{
    border:none;
    border-radius:24px;
    padding:10px;
}

.custom-input{
    border-radius:12px;
    background:#f7f7f7;
    border:1px solid #eee;
    padding:12px 16px;
    height:48px;
}

.custom-input:focus{
    box-shadow:none;
    border-color:#3498ff;
    background:white;
}

/* MODAL BUTTON */
.btn-save{
    background:#3498ff;
    color:white;
    border:none;
    padding:11px 22px;
    border-radius:12px;
    font-weight:600;
}

.btn-cancel{
    background:#f1f1f1;
    border:none;
    padding:11px 22px;
    border-radius:12px;
}

/* ALERT */
.alert-success-custom{
    border:none;
    border-radius:14px;
    padding:14px 18px;
    margin-bottom:20px;
    background:#e8fff1;
    color:#17a34a;
    font-weight:500;
}

</style>

@endsection