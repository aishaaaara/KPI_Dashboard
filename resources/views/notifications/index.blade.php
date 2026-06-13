@extends(auth()->user()->role === 'admin' ? 'admin.layouts.app' : 'member.layouts.app')

@section('content')

{{-- ===================== HEADER ===================== --}}
<div class="notif-page-header">
    <div>
        <h2>Notifikasi</h2>
        <p>Riwayat semua notifikasi kamu</p>
    </div>

    @if ($notifications->total() > 0)
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button type="submit" class="btn-read-all">
                <i class="bi bi-check2-all"></i>
                Tandai semua dibaca
            </button>
        </form>
    @endif
</div>

{{-- ===================== SUCCESS ALERT ===================== --}}
@if (session('success'))
    <div class="alert-success-bar">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>
@endif

{{-- ===================== NOTIFICATION LIST ===================== --}}
@if ($notifications->isEmpty())

    <div class="notif-empty-state">
        <i class="bi bi-bell-slash"></i>
        <p>Belum ada notifikasi</p>
    </div>

@else

    <div class="notif-card">

        @foreach ($notifications as $notif)

            @php
                $icons = [
                    'communication'       => ['icon' => 'bi-chat-left-text-fill', 'class' => 'icon--communication'],
                    'story_point'         => ['icon' => 'bi-journal-text',         'class' => 'icon--story_point'],
                    'workload'            => ['icon' => 'bi-kanban-fill',           'class' => 'icon--workload'],
                    'performance_insight' => ['icon' => 'bi-graph-up-arrow',        'class' => 'icon--performance_insight'],
                    'approval'            => ['icon' => 'bi-person-check-fill',     'class' => 'icon--approval'],
                ];

                $icon      = $icons[$notif->type]['icon']  ?? 'bi-bell';
                $iconClass = $icons[$notif->type]['class'] ?? '';
            @endphp

            <div class="notif-item {{ $notif->read_at ? '' : 'unread' }}">

                {{-- Icon --}}
                <div class="notif-icon {{ $iconClass }}">
                    <i class="bi {{ $icon }}"></i>
                </div>

                {{-- Body --}}
                <div class="notif-body">

                    <div class="notif-top">
                        <span class="notif-title">{{ $notif->title }}</span>
                        @if (!$notif->read_at)
                            <span class="unread-dot"></span>
                        @endif
                    </div>

                    <div class="notif-msg">{{ $notif->message }}</div>

                    <div class="notif-meta">
                        <i class="bi bi-clock"></i>
                        {{ $notif->created_at->diffForHumans() }}
                        · {{ $notif->created_at->format('d M Y, H:i') }}
                    </div>

                </div>

                {{-- Action --}}
                @if ($notif->url)
                    <a href="{{ $notif->url }}" class="notif-action-btn">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                @endif

            </div>

        @endforeach

    </div>

    {{-- PAGINATION --}}
    <div class="notif-pagination">
        {{ $notifications->links() }}
    </div>

@endif

{{-- ===================== STYLES ===================== --}}
<style>

    /* ----- Header ----- */
    .notif-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .notif-page-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        color: #111827;
    }

    .notif-page-header p {
        margin: 4px 0 0;
        font-size: 13px;
        color: #98a2b3;
    }

    /* ----- Button Read All ----- */
    .btn-read-all {
        height: 38px;
        padding: 0 16px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        color: #374151;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        transition: background .15s;
    }

    .btn-read-all:hover {
        background: #f3f4f6;
    }

    /* ----- Alert ----- */
    .alert-success-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        margin-bottom: 16px;
    }

    /* ----- Empty State ----- */
    .notif-empty-state {
        text-align: center;
        padding: 5rem 2rem;
        color: #b0b9c8;
    }

    .notif-empty-state i {
        font-size: 40px;
        display: block;
        margin-bottom: 12px;
        opacity: .4;
    }

    .notif-empty-state p {
        font-size: 13px;
    }

    /* ----- Card ----- */
    .notif-card {
        background: #fff;
        border: 1px solid #edf0f5;
        border-radius: 20px;
        overflow: hidden;
    }

    /* ----- Item ----- */
    .notif-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        transition: background .12s;
    }

    .notif-item:last-child {
        border-bottom: none;
    }

    .notif-item:hover {
        background: #fafafa;
    }

    .notif-item.unread {
        background: #eff6ff;
    }

    .notif-item.unread:hover {
        background: #dbeafe;
    }

    /* ----- Icon ----- */
    .notif-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
    }

    .icon--communication       { background: #eff6ff; color: #2563eb; }
    .icon--story_point         { background: #f0fdf4; color: #16a34a; }
    .icon--workload            { background: #fefce8; color: #ca8a04; }
    .icon--performance_insight { background: #fdf4ff; color: #9333ea; }
    .icon--approval            { background: #fff7ed; color: #ea580c; }

    /* ----- Body ----- */
    .notif-body {
        flex: 1;
        min-width: 0;
    }

    .notif-top {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 3px;
    }

    .notif-title {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
    }

    .unread-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #2563eb;
        flex-shrink: 0;
    }

    .notif-msg {
        font-size: 12px;
        color: #6b7280;
        line-height: 1.5;
        margin-bottom: 5px;
    }

    .notif-meta {
        font-size: 11px;
        color: #b0b9c8;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* ----- Action Button ----- */
    .notif-action-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #f3f4f6;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        flex-shrink: 0;
        transition: background .15s, color .15s;
    }

    .notif-action-btn:hover {
        background: #2563eb;
        color: #fff;
    }

    /* ----- Pagination ----- */
    .notif-pagination {
        margin-top: 16px;
        display: flex;
        justify-content: center;
    }

</style>

@endsection