@extends('admin.layouts.app')

@section('content')

<div class="aw">

    {{-- HEADER --}}
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

    {{-- ALERT SUKSES --}}
    @if(session('success'))
        <div class="aw-alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round" style="vertical-align:-2px;margin-right:6px" aria-hidden="true">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                <path d="M9 12l2 2l4 -4"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE CARD --}}
    <div class="aw-card">
        <div class="table-responsive">
            <table class="aw-table">
                <thead>
                    <tr>
                        <th style="width:44px">No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="width:110px">Status</th>
                        <th style="width:240px">Match Member</th>
                        <th style="width:200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td class="aw-num">{{ $loop->iteration }}</td>

                            <td>
                                <span class="aw-name">{{ $request->name }}</span>
                            </td>

                            <td>
                                <span class="aw-email">{{ $request->email }}</span>
                            </td>

                            <td>
                                @if($request->status == 'pending')
                                    <span class="badge badge-pending">Pending</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge badge-approved">Approved</span>
                                @else
                                    <span class="badge badge-rejected">Rejected</span>
                                @endif
                            </td>

                            <td>
                                @if($request->status == 'pending')
                                    {{-- Dropdown member hanya untuk yang masih pending --}}
                                    <form id="approve-form-{{ $request->id }}"
                                          action="{{ route('approvals.approve', $request->id) }}"
                                          method="POST">
                                        @csrf
                                        <select name="member_id"
                                                id="member-select-{{ $request->id }}"
                                                class="aw-select">
                                            <option value="">Select member</option>
                                            @foreach($members as $member)
                                                <option value="{{ $member->id }}">
                                                    {{ $member->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                @elseif($request->status == 'approved')
                                    <span class="badge badge-member">
                                        {{ $request->member->name ?? '-' }}
                                    </span>
                                @else
                                    <span class="aw-muted">—</span>
                                @endif
                            </td>

                            <td>
                                @if($request->status == 'pending')
                                    <div class="aw-actions">
                                        {{-- Tombol Approve: submit form member --}}
                                        <button type="submit"
                                                form="approve-form-{{ $request->id }}"
                                                class="btn btn-approve"
                                                onclick="return validateMember({{ $request->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                                 aria-hidden="true">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M5 12l5 5l10 -10"/>
                                            </svg>
                                            Approve
                                        </button>

                                        {{-- Tombol Reject --}}
                                        <form action="{{ route('approvals.reject', $request->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Reject this request?')">
                                            @csrf
                                            <button type="submit" class="btn btn-reject">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                                     aria-hidden="true">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M18 6l-12 12"/>
                                                    <path d="M6 6l12 12"/>
                                                </svg>
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="aw-muted">No action</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="aw-empty">
                                No registration request found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
/**
 * Validasi: pastikan member sudah dipilih sebelum submit approve.
 * Kalau belum, highlight border select merah dan batalkan submit.
 */
function validateMember(requestId) {
    const select = document.getElementById('member-select-' + requestId);
    if (!select || !select.value) {
        select.style.borderColor = '#E24B4A';
        select.focus();
        setTimeout(() => select.style.borderColor = '', 2000);
        return false;
    }
    return true;
}
</script>

<style>

/* ============================================================
   WRAPPER & HEADER
   ============================================================ */
.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:24px;
    gap:20px;
    flex-wrap:wrap;
}

.page-header h2{
    font-size:24px;
    font-weight:700;
    color:#111827;
    margin:0;
}

.page-header p{
    color:#98a2b3;
    font-size:14px;
    margin-top:4px;
    margin-bottom:0;
}

/* ============================================================
   ALERT
   ============================================================ */
.aw-alert {
    display: flex;
    align-items: center;
    background: #EAF3DE;
    color: #3B6D11;
    border: 0.5px solid #C0DD97;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 14px;
    margin-bottom: 16px;
}

/* ============================================================
   CARD & TABLE
   ============================================================ */
.aw-card {
    background: #fff;
    border: 0.5px solid #E5E7EB;
    border-radius: 14px;
    overflow: hidden;
}

.aw-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.aw-table thead th {
    background: #F9FAFB;
    color: #9CA3AF;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .05em;
    padding: 11px 16px;
    text-align: left;
    border-bottom: 0.5px solid #F3F4F6;
}

.aw-table tbody td {
    padding: 14px 16px;
    border-bottom: 0.5px solid #F3F4F6;
    vertical-align: middle;
    color: #374151;
}

.aw-table tbody tr:last-child td {
    border-bottom: none;
}

.aw-table tbody tr:hover td {
    background: #FAFAFA;
}

/* ============================================================
   CELL CONTENT
   ============================================================ */
.aw-num {
    color: #9CA3AF !important;
    font-size: 13px;
}

.aw-name {
    font-weight: 500;
    color: #111827;
    font-size: 14px;
}

.aw-email {
    font-size: 13px;
    color: #6B7280;
}

.aw-muted {
    font-size: 13px;
    color: #D1D5DB;
}

.aw-empty {
    text-align: center;
    padding: 48px 16px !important;
    color: #9CA3AF;
    font-size: 14px;
}

/* ============================================================
   BADGES
   ============================================================ */
.badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    line-height: 1.6;
}

.badge-pending  { background: #FAEEDA; color: #854F0B; }
.badge-approved { background: #EAF3DE; color: #3B6D11; }
.badge-rejected { background: #FCEBEB; color: #A32D2D; }
.badge-member   { background: #E6F1FB; color: #185FA5; }

/* ============================================================
   SELECT DROPDOWN
   ============================================================ */
.aw-select {
    width: 100%;
    font-size: 13px;
    padding: 7px 10px;
    border: 0.5px solid #D1D5DB;
    border-radius: 8px;
    background: #fff;
    color: #374151;
    outline: none;
    transition: border-color .15s;
    cursor: pointer;
}

.aw-select:focus {
    border-color: #9CA3AF;
}

/* ============================================================
   BUTTONS
   ============================================================ */
.aw-actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    border: 0.5px solid transparent;
    cursor: pointer;
    line-height: 1;
    transition: opacity .15s, transform .1s;
    white-space: nowrap;
}

.btn:hover   { opacity: .8; }
.btn:active  { transform: scale(.97); }

.btn-approve { background: #EAF3DE; color: #3B6D11; border-color: #C0DD97; }
.btn-reject  { background: #FCEBEB; color: #A32D2D; border-color: #F7C1C1; }

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 768px) {
    .aw {
        padding: 16px;
    }

    .aw-actions {
        flex-direction: column;
    }

    .aw-select {
        min-width: 160px;
    }
}

</style>

@endsection