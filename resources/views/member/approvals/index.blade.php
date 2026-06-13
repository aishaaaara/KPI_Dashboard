@extends('admin.layouts.app')

@section('content')


<div class="content-wrapper">

    {{-- HEADER --}}
    <div class="page-header">

        <div>

            <h2 class="page-title">

                Approval Request

            </h2>

            <p class="page-subtitle">

                Manage user registration requests

            </p>

        </div>

    </div>

    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif

    {{-- TABLE CARD --}}
    <div class="table-card">

        <div class="table-responsive">

            <table class="table align-middle custom-table">

                <thead>

                    <tr>

                        <th width="60">

                            No

                        </th>

                        <th>

                            Name

                        </th>

                        <th>

                            Email

                        </th>

                        <th width="140">

                            Status

                        </th>

                        <th width="250">

                            Match Member

                        </th>

                        <th width="220">

                            Action

                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($requests as $request)

                        <tr>

                            <td>

                                {{ $loop->iteration }}

                            </td>

                            <td>

                                <div class="fw-semibold">

                                    {{ $request->name }}

                                </div>

                            </td>

                            <td>

                                {{ $request->email }}

                            </td>

                            <td>

                                @if($request->status == 'pending')

                                    <span class="badge bg-warning">

                                        Pending

                                    </span>

                                @elseif($request->status == 'approved')

                                    <span class="badge bg-success">

                                        Approved

                                    </span>

                                @else

                                    <span class="badge bg-danger">

                                        Rejected

                                    </span>

                                @endif

                            </td>

                            <td>

                                @if($request->status == 'pending')

                                    <form action="{{ route('approvals.approve',$request->id) }}"
                                          method="POST">

                                        @csrf

                                        <select name="member_id"
                                                class="form-select member-select"
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

                            </td>

                            <td>

                                    <div class="action-group">

                                        <button type="submit"
                                                class="btn btn-success btn-sm">

                                            Approve

                                        </button>

                                    </form>

                                    <form action="{{ route('approvals.reject',$request->id) }}"
                                          method="POST">

                                        @csrf

                                        <button type="submit"
                                                class="btn btn-danger btn-sm">

                                            Reject

                                        </button>

                                    </form>

                                </div>

                            </td>

                            @else

                                    <span class="badge bg-primary">

                                        {{ $request->member->name ?? '-' }}

                                    </span>

                            </td>

                            <td>

                                <span class="text-muted">

                                    No Action

                                </span>

                            </td>

                            @endif

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6"
                                class="text-center py-5">

                                No registration request found

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

<style>

.content-wrapper{
    padding:24px;
}

.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:24px;
}

.page-title{
    font-size:28px;
    font-weight:700;
    color:#111827;
    margin-bottom:4px;
}

.page-subtitle{
    color:#6B7280;
    margin-bottom:0;
}

.table-card{
    background:#fff;
    border-radius:18px;
    padding:20px;
    box-shadow:0 2px 10px rgba(0,0,0,.05);
}

.custom-table{
    margin-bottom:0;
}

.custom-table thead th{
    background:#F9FAFB;
    border:none;
    color:#6B7280;
    font-size:13px;
    font-weight:600;
    padding:16px;
}

.custom-table tbody td{
    padding:16px;
    vertical-align:middle;
    border-color:#F3F4F6;
}

.member-select{
    min-width:220px;
}

.action-group{
    display:flex;
    gap:8px;
}

.badge{
    padding:8px 12px;
    border-radius:8px;
    font-size:12px;
}

.alert-success{
    border:none;
    border-radius:12px;
}

@media(max-width:768px){

    .content-wrapper{
        padding:16px;
    }

    .action-group{
        flex-direction:column;
    }

    .member-select{
        min-width:180px;
    }

}

</style>

@endsection