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

        <div class="header-info">
            <h2>Team Member</h2>
            <p>Members Management</p>
        </div>

        <div class="header-actions">

            <a href="{{ route('admin.members.template') }}"
               class="btn-custom btn-template">
                <i class="bi bi-file-earmark-excel"></i>
                Template
            </a>

            <a href="{{ route('admin.members.export') }}"
               class="btn-custom btn-export">
                <i class="bi bi-download"></i>
                Export
            </a>

            <button type="button"
                    class="btn-custom btn-import"
                    data-bs-toggle="modal"
                    data-bs-target="#importModal">
                <i class="bi bi-upload"></i>
                Import
            </button>

            <button type="button"
                    class="btn-custom btn-add"
                    data-bs-toggle="modal"
                    data-bs-target="#addMemberModal">
                <i class="bi bi-plus-circle"></i>
                Add Member
            </button>

        </div>

    </div>

    {{-- SEARCH & FILTER --}}
    <div class="filter-card">

        <form method="GET"
              action="{{ route('admin.members.index') }}"
              id="filterForm"
              class="filter-wrapper">

            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text"
                       name="search"
                       id="searchInput"
                       placeholder="Search member..."
                       value="{{ request('search') }}">
            </div>

            <select name="team_id" class="filter-select auto-filter">
                <option value="">All Team</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}"
                        {{ request('team_id') == $team->id ? 'selected' : '' }}>
                        {{ $team->name }}
                    </option>
                @endforeach
            </select>

            <select name="employment_type_id" class="filter-select auto-filter">
                <option value="">All Type</option>
                @foreach($employmentTypes as $type)
                    <option value="{{ $type->id }}"
                        {{ request('employment_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>

            <a href="{{ route('admin.members.index') }}" class="btn-reset-filter">
                <i class="bi bi-arrow-clockwise"></i>
            </a>

        </form>

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

                        <td class="member-name">{{ $member->name }}</td>

                        <td>{{ $member->position->name }}</td>

                        <td>
                            @if(strtolower($member->employmentType->name) == 'full-time')
                                <span class="badge-full">{{ $member->employmentType->name }}</span>
                            @else
                                <span class="badge-intern">{{ $member->employmentType->name }}</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge-team">{{ $member->team->name }}</span>
                        </td>

                        <td>{{ \Carbon\Carbon::parse($member->join_date)->format('d/m/y') }}</td>

                        <td>{{ \Carbon\Carbon::parse($member->end_date)->format('d/m/y') }}</td>

                        <td>
                            <div class="action-group">

                                <button class="btn-action btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $member->id }}">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>

                                <button class="btn-action btn-delete"
                                        onclick="confirmDelete({{ $member->id }}, '{{ $member->name }}')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>

                                <form id="deleteForm-{{ $member->id }}"
                                      action="{{ route('admin.members.destroy', $member->id) }}"
                                      method="POST"
                                      style="display:none">
                                    @csrf
                                    @method('DELETE')
                                </form>

                            </div>
                        </td>

                    </tr>

                    {{-- EDIT MODAL --}}
                    <div class="modal fade" id="editModal{{ $member->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content custom-modal">

                                <div class="modal-header border-0">
                                    <div>
                                        <h4 class="fw-bold">Edit Employee</h4>
                                        <small class="text-muted">Update employee data</small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <form action="{{ route('admin.members.update', $member->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-body">
                                        <div class="row">

                                            <div class="col-md-6 mb-3">
                                                <label>Name *</label>
                                                <input type="text" name="name"
                                                       value="{{ $member->name }}"
                                                       class="form-control custom-input">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Position *</label>
                                                <select name="position_id" class="form-select custom-input">
                                                    @foreach($positions as $position)
                                                        <option value="{{ $position->id }}"
                                                            {{ $member->position_id == $position->id ? 'selected' : '' }}>
                                                            {{ $position->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Team *</label>
                                                <select name="team_id" class="form-select custom-input">
                                                    @foreach($teams as $team)
                                                        <option value="{{ $team->id }}"
                                                            {{ $member->team_id == $team->id ? 'selected' : '' }}>
                                                            {{ $team->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Type *</label>
                                                <select name="employment_type_id" class="form-select custom-input">
                                                    @foreach($employmentTypes as $type)
                                                        <option value="{{ $type->id }}"
                                                            {{ $member->employment_type_id == $type->id ? 'selected' : '' }}>
                                                            {{ $type->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Join Date</label>
                                                <input type="date" name="join_date"
                                                       value="{{ $member->join_date }}"
                                                       class="form-control custom-input">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>End Date</label>
                                                <input type="date" name="end_date"
                                                       value="{{ $member->end_date }}"
                                                       class="form-control custom-input">
                                            </div>

                                        </div>
                                    </div>

                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn-save">Update</button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                    @empty

                    <tr>
                        <td colspan="7" class="empty-data">No Data</td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- ADD MEMBER MODAL --}}
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content custom-modal">

            <div class="modal-header border-0">
                <div>
                    <h4 class="fw-bold">Add Employee</h4>
                    <small class="text-muted">Create new employee data</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.members.store') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Name *</label>
                            <input type="text" name="name"
                                   class="form-control custom-input"
                                   required placeholder="Enter Full Name">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Position *</label>
                            <select name="position_id" class="form-select custom-input" required>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Team *</label>
                            <select name="team_id" class="form-select custom-input" required>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Type *</label>
                            <select name="employment_type_id" class="form-select custom-input" required>
                                @foreach($employmentTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Join Date</label>
                            <input type="date" name="join_date" class="form-control custom-input">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control custom-input">
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-save">Save Member</button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- IMPORT MODAL --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">

            <div class="modal-header border-0">
                <div>
                    <h4 class="fw-bold">Import Excel</h4>
                    <small class="text-muted">Upload file .xlsx atau .xls</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.members.import') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <input type="file"
                           name="file"
                           class="form-control custom-input"
                           style="height:auto;padding:12px"
                           accept=".xlsx,.xls"
                           required>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-save">Import</button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- MODAL DELETE KONFIRMASI --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content" style="border:none; border-radius:24px; overflow:hidden;">
            <div class="modal-body text-center" style="padding:40px 32px 32px">

                <div class="delete-icon-wrap">
                    <i class="bi bi-trash3-fill"></i>
                </div>

                <h5 class="delete-title">Delete Member</h5>

                <p class="delete-msg">
                    Are you sure you want to delete
                    <strong id="deleteMemberName"></strong>?
                    <br>
                    <span style="color:#ef4444; font-size:12px;">
                        This action cannot be undone.
                    </span>
                </p>

                <div class="delete-actions">
                    <button type="button" class="btn-del-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-del-confirm" id="btnConfirmDelete">
                        <i class="bi bi-trash3"></i>
                        Yes, Delete
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- MODAL IMPORT ERROR --}}
@if(session('import_error'))
<div class="modal fade" id="importErrorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content" style="border:none; border-radius:24px; overflow:hidden;">
            <div class="modal-body text-center" style="padding:40px 32px 32px">

                <div class="import-error-icon-wrap">
                    <i class="bi bi-x-circle-fill"></i>
                </div>

                <h5 class="delete-title">Import Gagal</h5>

                <p class="delete-msg">
                    {{ session('import_error') }}
                </p>

                <div class="delete-actions" style="justify-content:center">
                    <button type="button" class="btn-del-cancel" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('admin.members.template') }}"
                       class="btn-del-confirm"
                       style="text-decoration:none; background:#2563eb">
                        <i class="bi bi-file-earmark-excel"></i>
                        Download Template
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endif

<style>
body { background:#f5f6fa; overflow-x:hidden; }

.member-header {
    display:flex; justify-content:space-between;
    align-items:center; margin-bottom:24px;
    flex-wrap:wrap; gap:15px;
}

.header-info h2 { font-size:24px; font-weight:700; margin:0; color:#111827; }
.header-info p  { margin:4px 0 0; color:#98a2b3; font-size:13px; }

.header-actions { display:flex; gap:12px; flex-wrap:wrap; }

.btn-custom {
    display:flex; align-items:center; gap:8px;
    border:none; text-decoration:none;
    padding:12px 18px; border-radius:14px;
    font-size:14px; font-weight:600;
    transition:all .25s ease; cursor:pointer;
}
.btn-custom i { font-size:15px; }

.btn-template { height:40px; padding:0 16px; border-radius:12px; background:#f8fafc; color:#475569; border:1px solid #e2e8f0; }
.btn-template:hover { background:#475569; color:white; transform:translateY(-2px); }

.btn-export { height:40px; padding:0 16px; border-radius:12px; background:#ecfdf3; color:#16a34a; border:1px solid #bbf7d0; }
.btn-export:hover { background:#16a34a; color:white; transform:translateY(-2px); }

.btn-import { height:40px; padding:0 16px; border-radius:12px; background:#fff7ed; color:#ea580c; border:1px solid #fed7aa; }
.btn-import:hover { background:#ea580c; color:white; transform:translateY(-2px); }

.btn-add { height:40px; padding:0 16px; border-radius:12px; background:#2563eb; color:#fff; font-size:13px; font-weight:600; display:flex; align-items:center; gap:7px; cursor:pointer; transition:background .15s; }
.btn-add:hover { background:#2388f5; transform:translateY(-2px); }

.filter-card { background:white; border-radius:20px; padding:18px; margin-bottom:20px; box-shadow:0 4px 18px rgba(0,0,0,.03); }
.filter-wrapper { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }

.search-box { flex:1; min-width:260px; position:relative; }
.search-box i { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#9ca3af; }
.search-box input { width:100%; height:48px; border:1px solid #e5e7eb; border-radius:14px; padding-left:45px; padding-right:16px; font-size:14px; background:#fafafa; }
.search-box input:focus { outline:none; border-color:#3498ff; background:white; }

.filter-select { min-width:180px; height:48px; border:1px solid #e5e7eb; border-radius:14px; padding:0 14px; background:white; font-size:14px; }
.filter-select:focus { outline:none; border-color:#3498ff; }

.btn-reset-filter { width:48px; height:48px; border-radius:14px; background:#f3f4f6; display:flex; align-items:center; justify-content:center; color:#6b7280; text-decoration:none; transition:.2s; }
.btn-reset-filter:hover { background:#e5e7eb; }

.table-section { background:white; border-radius:24px; padding:24px; width:100%; box-shadow:0 4px 18px rgba(0,0,0,0.03); }
.table-responsive { width:100%; overflow-x:auto; }

.custom-table { width:100%; min-width:850px; border-collapse:collapse; }
.custom-table thead tr { background:#f7f8fc; }
.custom-table thead th { padding:16px; font-size:12px; font-weight:600; color:#98a2b3; text-align:left; }
.custom-table tbody tr { border-bottom:1px solid #f1f1f1; transition:0.2s; }
.custom-table tbody tr:hover { background:#fafcff; }
.custom-table tbody td { padding:18px 16px; font-size:13px; color:#374151; vertical-align:middle; }

.member-name { min-width:160px; font-weight:600; }

.badge-full, .badge-intern, .badge-team {
    display:inline-flex; align-items:center; justify-content:center;
    padding:7px 14px; border-radius:999px; font-size:11px; font-weight:600; white-space:nowrap;
}
.badge-full   { background:#dcfce7; color:#16a34a; }
.badge-intern { background:#fef3c7; color:#f59e0b; }
.badge-team   { background:#edf4ff; color:#3b82f6; }

.action-group { display:flex; align-items:center; gap:8px; }

.btn-action { width:36px; height:36px; border:none; border-radius:12px; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:0.2s; }
.btn-edit   { background:#eef4ff; color:#3b82f6; }
.btn-delete { background:#ffecec; color:#ef4444; }
.btn-action:hover { transform:translateY(-2px); }

.empty-data { text-align:center; padding:30px !important; color:#999; }

.custom-modal { border:none; border-radius:24px; padding:10px; }
.custom-input { border-radius:12px; background:#f7f7f7; border:1px solid #eee; padding:12px 16px; height:48px; }
.custom-input:focus { box-shadow:none; border-color:#3498ff; background:white; }

.btn-save   { background:#3498ff; color:white; border:none; padding:11px 22px; border-radius:12px; font-weight:600; }
.btn-cancel { background:#f1f1f1; border:none; padding:11px 22px; border-radius:12px; }

.alert-success-custom { border:none; border-radius:14px; padding:14px 18px; margin-bottom:20px; background:#e8fff1; color:#17a34a; font-weight:500; }

/* Delete modal */
.delete-icon-wrap {
    width:80px; height:80px; border-radius:50%;
    background:#fef2f2; display:flex; align-items:center;
    justify-content:center; margin:0 auto 20px;
    border:6px solid #fee2e2;
}
.delete-icon-wrap i { font-size:30px; color:#ef4444; }

/* Import error modal */
.import-error-icon-wrap {
    width:80px; height:80px; border-radius:50%;
    background:#fef2f2; display:flex; align-items:center;
    justify-content:center; margin:0 auto 20px;
    border:6px solid #fee2e2;
}
.import-error-icon-wrap i { font-size:30px; color:#ef4444; }

.delete-title  { font-size:18px; font-weight:700; color:#111827; margin-bottom:10px; }
.delete-msg    { font-size:13px; color:#6b7280; line-height:1.7; margin-bottom:28px; }
.delete-actions { display:flex; gap:10px; justify-content:center; }

.btn-del-cancel {
    height:42px; padding:0 24px; border:1px solid #e5e7eb;
    border-radius:12px; background:#fff; color:#374151;
    font-size:13px; font-weight:600; cursor:pointer; transition:background .15s;
}
.btn-del-cancel:hover { background:#f3f4f6; }

.btn-del-confirm {
    height:42px; padding:0 24px; border:none;
    border-radius:12px; background:#ef4444; color:#fff;
    font-size:13px; font-weight:600; cursor:pointer;
    display:flex; align-items:center; gap:7px; transition:background .15s;
}
.btn-del-confirm:hover { background:#dc2626; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Filter & search
    const form        = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');
    let timer;

    searchInput.addEventListener('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(function () { form.submit(); }, 500);
    });

    document.querySelectorAll('.auto-filter').forEach(function (item) {
        item.addEventListener('change', function () { form.submit(); });
    });

    // Confirm delete
    document.getElementById('btnConfirmDelete').addEventListener('click', function () {
        if (deleteTargetId) {
            document.getElementById('deleteForm-' + deleteTargetId).submit();
        }
    });

    // Auto buka modal import error kalau ada session
    @if(session('import_error'))
        new bootstrap.Modal(document.getElementById('importErrorModal')).show();
    @endif

});

// Delete
let deleteTargetId = null;

function confirmDelete(id, name) {
    deleteTargetId = id;
    document.getElementById('deleteMemberName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
}
</script>

@endsection