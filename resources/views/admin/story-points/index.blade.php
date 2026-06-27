@extends('admin.layouts.app')

@section('content')

<div class="container-fluid py-4">


    {{-- HEADER --}}
    <div class="story-header">

        <div>
            <h2>Story Points</h2>
            <p>Story Point Management</p>
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
                    data-bs-target="#addStoryPointModal">
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
                    $totalData = $storyPointCounts[$period->id] ?? 0;
                @endphp

                <div class="period-card {{ $selectedPeriod == $period->id ? 'active-period' : '' }}"
                     id="period-{{ $period->id }}">

                    <a href="?period_id={{ $period->id }}" class="period-link">
                        <i class="bi bi-calendar3"></i>
                        <span class="period-month">{{ substr($period->month, 0, 3) }} {{ $period->year }}</span>
                    </a>

                    <span class="period-total">{{ $totalData }}</span>

                    <form action="{{ route('story-points.period.destroy', $period->id) }}" method="POST" style="margin:0">
                        @csrf
                        @method('DELETE')
                        <button class="btn-delete-period" type="submit"
                                onclick="return confirm('Delete this period?')">
                            <i class="bi bi-trash3"></i>
                        </button>
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

                        @php
                            $achievement = $storyPoint->target > 0
                                ? round(($storyPoint->totals / $storyPoint->target) * 100)
                                : 0;

                            $status = $achievement >= 110 ? 'Exceeded' :
                                ($achievement >= 90 ? 'Achieved' :
                                ($achievement >= 75 ? 'Near Target' : 'Below Target'));

                            $statusClass = $achievement >= 110 ? 'status-success' :
                                ($achievement >= 90 ? 'status-primary' :
                                ($achievement >= 75 ? 'status-warning' : 'status-danger'));

                            $periodDate = \Carbon\Carbon::createFromDate(
                                $storyPoint->period->year,
                                date('n', strtotime($storyPoint->period->month)),
                                1
                            );
                            $isLocked = $periodDate->lt(now()->startOfMonth());
                        @endphp

                        <tr>

                            <td>
                                <div class="eid-box">{{ $storyPoint->member->eid }}</div>
                            </td>

                            <td>
                                <div class="member-box">
                                    <div>
                                        <div class="member-name">{{ $storyPoint->member->name }}</div>
                                        <small class="member-label">{{ $storyPoint->member->position->name }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="point-number">{{ number_format($storyPoint->target) }}</span>
                            </td>

                            <td>
                                <span class="point-number total-point">{{ number_format($storyPoint->totals) }}</span>
                            </td>

                            <td>
                                <div class="achievement-wrapper">
                                    <div class="progress achievement-progress">
                                        <div class="progress-bar" style="width: {{ min($achievement,100) }}%"></div>
                                    </div>
                                    <span>{{ $storyPoint->summary }}%</span>
                                </div>
                            </td>

                            <td>
                                <span class="status-badge {{ $statusClass }}">{{ $status }}</span>
                            </td>

                            <td>
                                <div class="action-group">

                                    @if (!$isLocked)
                                        <button type="button" class="btn-edit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editModal{{ $storyPoint->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    @else
                                        <button class="btn-locked" disabled title="Periode terkunci">
                                            <i class="bi bi-lock-fill"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="empty-data">
                                <div class="empty-wrapper">
                                    <i class="bi bi-folder-x"></i>
                                    <p>No Story Point Data</p>
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- EDIT MODALS --}}
@foreach($storyPoints as $storyPoint)

<div class="modal fade"
     id="editModal{{ $storyPoint->id }}"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">

            <form action="{{ route('admin.story-points.update', $storyPoint->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header border-0">
                    <h4 class="fw-bold mb-0">Edit Story Point</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Target</label>
                        <input type="number"
                               name="target"
                               class="form-control custom-input"
                               value="{{ $storyPoint->target }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total</label>
                        <input type="number"
                               name="totals"
                               class="form-control custom-input"
                               value="{{ $storyPoint->totals }}"
                               required>
                    </div>

                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-save">Update Story Point</button>
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

            <form action="{{ route('story-points.period.store') }}" method="POST">
                @csrf

                <div class="modal-header border-0">
                    <h4 class="fw-bold">Add New Month</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Month</label>
                        <select id="monthSelect" name="month" class="form-select" required>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Year</label>
                        <select id="yearSelect" name="year" class="form-select" required>
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

{{-- MODAL ADD STORY POINT --}}
<div class="modal fade" id="addStoryPointModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content custom-modal">

            <form action="{{ route('admin.story-points.store') }}" method="POST">
                @csrf

                <div class="modal-header border-0">
                    <div>
                        <h4 class="fw-bold">Add Story Point</h4>
                        <small class="text-muted">Input story point data</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Member</label>
                            <select name="member_id" class="form-select custom-input" required>
                                <option value="">Select Member</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Period</label>
                            <select name="period_id" class="form-select custom-input" required>
                                <option value="">Select Period</option>
                                @foreach($periods as $period)
                                    <option value="{{ $period->id }}">{{ $period->month }} {{ $period->year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Target</label>
                            <input type="number" name="target" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total</label>
                            <input type="number" name="totals" class="form-control custom-input" required>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-save">Save Story Point</button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>

const currentYear  = {{ now()->year }};
const currentMonth = {{ now()->month }};

const months = [
    'January','February','March','April','May','June',
    'July','August','September','October','November','December'
];

const existingPeriods = @json(
    $existingPeriods->map(fn($p) => ['month' => $p->month, 'year' => $p->year])
);

function loadMonths() {
    const selectedYear = parseInt(document.getElementById('yearSelect').value);
    const monthSelect  = document.getElementById('monthSelect');

    monthSelect.innerHTML = '<option value="">Select Month</option>';

    months.forEach((month, index) => {
        const monthNumber  = index + 1;
        const isPastMonth  = selectedYear === currentYear && monthNumber < currentMonth;
        const alreadyExists = existingPeriods.some(p => p.month === month && parseInt(p.year) === selectedYear);

        if (!isPastMonth && !alreadyExists) {
            monthSelect.innerHTML += `<option value="${month}">${month}</option>`;
        }
    });
}

document.getElementById('yearSelect').addEventListener('change', loadMonths);
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
        wrapper.scrollLeft = active.offsetLeft - (wrapper.offsetWidth / 2) + (active.offsetWidth / 2);
    } else {
        wrapper.scrollLeft = wrapper.scrollWidth;
    }
});

</script>

<style>

.story-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    gap: 20px;
    flex-wrap: wrap;
}

.story-header h2 {
    font-size: 24px;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.story-header p {
    color: #98a2b3;
    font-size: 14px;
    margin-top: 4px;
    margin-bottom: 0;
}

.header-action {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.btn-add-month,
.btn-add-data {
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
.btn-add-data:hover {
    background: #1d4ed8;
}

/* Period */
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

.comm-period .period-link i { font-size: 13px; color: #6b7280; }

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

.comm-period .btn-delete-period:hover { color: #ef4444; }

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

/* Table */
.table-section {
    background: white;
    border-radius: 24px;
    padding: 24px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.03);
}

.table-responsive { overflow-x: auto; }

.custom-table {
    width: 100%;
    border-collapse: collapse;
}

.custom-table thead tr { background: #f7f8fc; }

.custom-table th {
    padding: 16px;
    color: #98a2b3;
    font-size: 12px;
    font-weight: 700;
    text-align: left;
}

.custom-table tbody tr { border-bottom: 1px solid #f1f1f1; height: 72px; }
.custom-table tbody tr:hover { background: #fafcff; }

.custom-table td {
    padding: 18px 16px;
    font-size: 13px;
    color: #374151;
    vertical-align: middle;
}

.custom-table td:last-child { text-align: center; }

.eid-box { font-weight: 700; }

.member-box { display: flex; align-items: center; gap: 12px; }
.member-name { font-weight: 700; }
.member-label { color: #98a2b3; font-size: 11px; }

.point-number { font-size: 15px; font-weight: 700; color: #374151; }
.total-point { color: #2563eb; }

.achievement-wrapper { min-width: 120px; }

.achievement-progress {
    height: 8px;
    margin-bottom: 5px;
    border-radius: 999px;
    background: #e5e7eb;
}

.achievement-progress .progress-bar { background: #3b82f6; }

.status-badge {
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.status-success { background: #dcfce7; color: #15803d; }
.status-primary { background: #dbeafe; color: #1d4ed8; }
.status-warning { background: #fef3c7; color: #b45309; }
.status-danger  { background: #fee2e2; color: #dc2626; }

.action-group {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.action-group form { margin: 0; }

.btn-edit,
.btn-delete {
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: .25s;
}

.btn-edit  { background: #eef6ff; color: #3498ff; }
.btn-delete { background: #fff1f1; color: #ef4444; }
.btn-edit:hover  { background: #dbeafe; }
.btn-delete:hover { background: #fee2e2; }

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

/* Modal */
.custom-modal {
    border: none;
    border-radius: 24px;
    padding: 10px;
    box-shadow: 0 20px 60px rgba(0,0,0,.15);
}

.custom-input {
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    height: 50px;
    padding: 0 14px;
}

.custom-input:focus { box-shadow: none; border-color: #3498ff; }

.form-label { font-size: 13px; font-weight: 600; margin-bottom: 8px; }

.btn-save {
    background: #3498ff;
    color: white;
    border: none;
    padding: 12px 22px;
    border-radius: 14px;
    font-weight: 600;
    transition: .25s;
}

.btn-save:hover { background: #2563eb; }

.btn-cancel {
    background: #f3f4f6;
    border: none;
    padding: 12px 22px;
    border-radius: 14px;
}

/* Alert */
.alert-success-custom {
    background: #e8fff1;
    color: #16a34a;
    padding: 14px 18px;
    border-radius: 14px;
    margin-bottom: 18px;
}

.alert-error-custom {
    background: #ffecec;
    color: #ef4444;
    padding: 14px 18px;
    border-radius: 14px;
    margin-bottom: 18px;
}

/* Empty */
.empty-data { text-align: center; padding: 40px !important; }

.empty-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #98a2b3;
}

.empty-wrapper i { font-size: 42px; }

/* Scrollbar */
.table-responsive::-webkit-scrollbar { height: 8px; }
.table-responsive::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 999px; }

</style>

@endsection