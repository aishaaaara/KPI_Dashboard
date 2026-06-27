<div class="sidebar" id="sidebar">

    <div>

        {{-- TOP --}}
        <div class="sidebar-top">

            {{-- LOGO --}}
            <div class="logo-box">

                <h4 class="logo-text">
                    Dev KPI Tracker
                </h4>

                <small class="logo-sub">
                    Team Analytics
                </small>

            </div>

            {{-- TOGGLE --}}
            <button class="toggle-btn"
                    id="toggleSidebar">

                <i class="bi bi-list"></i>

            </button>

        </div>

        {{-- NAVIGATION --}}
        <div class="menu-title">
            Navigation
        </div>

        <ul class="sidebar-menu">

            {{-- DASHBOARD --}}
            <li>
                <a href="/member/dashboard" title="Dashboard"
                   class="{{ request()->is('member/dashboard') ? 'active' : '' }}">

                    <i class="bi bi-grid-fill"></i>

                    <span>
                        Dashboard
                    </span>

                </a>
            </li>

            {{-- MEMBERS --}}
            <li>
                <a href="/member/members" title="Team Member"
                   class="{{ request()->is('member/members*') ? 'active' : '' }}">

                    <i class="bi bi-people-fill"></i>

                    <span>
                        Team Member
                    </span>

                </a>
            </li>

        </ul>

        {{-- METRICS --}}
        <div class="menu-title">
            Metrics
        </div>

        <ul class="sidebar-menu">

            {{-- COMMUNICATION --}}
            <li>
                <a href="/member/communication" title="Communication"
                   class="{{ request()->is('member/communication*') ? 'active' : '' }}">

                    <i class="bi bi-chat-left-text-fill"></i>

                    <span>
                        Communication
                    </span>

                </a>
            </li>

            {{-- STORY POINT --}}
            <li>
                <a href="/member/story-points" title="Story Points"
                   class="{{ request()->is('member/story-points*') ? 'active' : '' }}">

                    <i class="bi bi-journal-text"></i>

                    <span>
                        Story Points
                    </span>

                </a>
            </li>

            {{-- WORKLOAD --}}
            <li>
                <a href="/member/workload" title="Workload"
                   class="{{ request()->is('member/workload*') ? 'active' : '' }}">

                    <i class="bi bi-kanban-fill"></i>

                    <span>
                        Workload
                    </span>

                </a>
            </li>

        </ul>

        {{-- OTHERS --}}
        <div class="menu-title">
            Others
        </div>

        <ul class="sidebar-menu">

            {{-- PERFORMANCE --}}
            <li>
                <a href="/member/performance-insight" title="Performance Insight"
                class="{{ request()->is('member/performance-insight*') ? 'active' : '' }}">

                    <i class="bi bi-graph-up-arrow"></i>

                    <span>
                        Performance Insight
                    </span>

                </a>
            </li>
        </ul>

</div>


{{-- USER PROFILE --}}
<div class="sidebar-user">
    <a href="/member/profile" class="user-info" id="userInfoBtn" title="Lihat Profil">
        <div class="user-avatar">
            <i class="bi bi-person-circle"></i>
        </div>

        <div class="user-detail">
            <div class="user-name">{{ auth()->user()->name }}</div>
            <small>Member Access</small>
        </div>

    </a>

    <button type="button"
            class="logout-btn border-0 bg-transparent"
            title="Logout"
            data-bs-toggle="modal"
            data-bs-target="#logoutConfirmModal">
        <i class="bi bi-box-arrow-right"></i>
    </button>

</div>

</div>
        {{-- MODAL KONFIRMASI LOGOUT --}}
    <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
            <div class="modal-content" style="border:none; border-radius:24px; overflow:hidden;">
                <div class="modal-body text-center" style="padding:40px 32px 32px">

                    <div class="logout-icon-wrap">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>

                    <h5 class="logout-title">Konfirmasi Logout</h5>

                    <p class="logout-msg">
                        Apakah kamu yakin ingin keluar dari sesi ini?
                        <br>
                        <span style="color:#6b7280; font-size:12px;">
                            Kamu perlu login kembali untuk mengakses sistem.
                        </span>
                    </p>

                    <div class="logout-actions">
                        <button type="button" class="btn-logout-cancel" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <form action="{{ route('logout') }}" method="POST" style="margin:0">
                            @csrf
                            <button type="submit" class="btn-logout-confirm">
                                <i class="bi bi-box-arrow-right"></i>
                                Ya, Logout
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

<style>
.sidebar {
    width: 260px;
    height: 100vh;
    background: #082B5B;
    padding: 24px 18px 18px;
    color: white;
    position: sticky;
    top: 0;
    transition: 0.3s;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

/* COLLAPSE */
.sidebar.collapsed {
    width: 88px;
}

/* TOP */
.sidebar-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 30px;
    flex-shrink: 0; /* tidak boleh menyusut */
}

/* LOGO */
.logo-box { transition: 0.3s; }

.logo-box h4 {
    font-size: 18px;
    margin-bottom: 4px;
    font-weight: 700;
    white-space: nowrap;
}

.logo-box small {
    opacity: 0.7;
    font-size: 13px;
    white-space: nowrap;
}

/* HIDE TEXT */
.sidebar.collapsed .logo-text,
.sidebar.collapsed .logo-sub,
.sidebar.collapsed .menu-title,
.sidebar.collapsed .sidebar-menu span {
    display: none;
}

/* ── INI YANG FIX ── area menu bisa scroll, user card tetap di bawah */
.sidebar > div:first-child {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    min-height: 0; /* wajib ada supaya flex mau scroll */
    scrollbar-width: none;
    padding-bottom: 8px;
}

.sidebar > div:first-child::-webkit-scrollbar {
    display: none;
}

/* MENU TITLE */
.menu-title {
    font-size: 12px;
    opacity: 0.6;
    margin-bottom: 10px;
    margin-top: 24px;
    text-transform: uppercase;
    letter-spacing: 1px;
    flex-shrink: 0;
}

/* MENU */
.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-menu li {
    margin-bottom: 10px;
}

.sidebar-menu a {
    text-decoration: none;
    color: white;
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 14px;
    border-radius: 14px;
    transition: 0.3s;
    font-size: 14px;
    font-weight: 500;
    white-space: nowrap;
}

/* CENTER ICON saat collapsed */
.sidebar.collapsed .sidebar-menu a {
    width: 48px;        /* ganti dari 56px */
    height: 48px;       /* ganti dari 56px */
    margin: 0 auto;     /* ganti dari auto */
    padding: 0;
    justify-content: center;
    border-radius: 18px;
}

.sidebar.collapsed .sidebar-menu i {
    margin: 0;
    min-width: auto;
    font-size: 20px;
}

.sidebar.collapsed .sidebar-menu a.active {
    background: #0F4C9C;
    color: white;
    
}

.sidebar.collapsed .sidebar-menu li {
    display: flex;
    justify-content: center;
    margin-bottom: 14px;
}

.sidebar.collapsed .toggle-btn { margin: auto; }
.sidebar.collapsed .sidebar-top { justify-content: center; }

/* HOVER & ACTIVE */
.sidebar-menu a:hover { background: #0F4C9C; color: white; }
.sidebar-menu .active { background: #0F4C9C; color: white; }

/* ICON */
.sidebar-menu i {
    font-size: 18px;
    min-width: 20px;
}

/* TOGGLE BUTTON */
.toggle-btn {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 12px;
    background: #0F4C9C;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    cursor: pointer;
    transition: 0.3s;
    flex-shrink: 0;
}

.toggle-btn:hover { background: #1a5db5; }

/* MOBILE */
@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        z-index: 999;
        left: -260px;
    }
    .sidebar.show { left: 0; }
}

/* USER CARD — flex-shrink:0 supaya tidak terpotong */
.sidebar-user {
    margin-top: 12px;
    flex-shrink: 0; /* ← kunci utama, tidak boleh menyusut */
    background: white;
    border-radius: 12px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    flex: 1;
    min-width: 0;
    border-radius: 8px;
    padding: 4px 6px;
    margin: -4px -6px;
    transition: background .15s;
}

.user-info:hover { background: #f3f4f6; }

.user-avatar {
    font-size: 34px;
    color: #6b7280;
    line-height: 1;
    flex-shrink: 0;
    transition: color .15s;
}

.user-info:hover .user-avatar { color: #2563eb; }

.user-detail {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.user-name {
    color: #1f2937;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-detail small {
    color: #6b7280;
    font-size: 11px;
}

.logout-btn {
    color: #9ca3af;
    font-size: 20px;
    transition: .2s;
    flex-shrink: 0;
    padding: 4px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logout-btn:hover {
    color: #ef4444;
    background: #fef2f2;
}

/* COLLAPSED */
.sidebar.collapsed .user-detail { display: none; }
.sidebar.collapsed .sidebar-user { justify-content: center; padding: 10px; }
.sidebar.collapsed .logout-btn { display: none; }
.sidebar.collapsed .user-info { margin: 0; padding: 0; }

/* ── LOGOUT MODAL ── */
.logout-icon-wrap {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: #FEF3C7;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.logout-icon-wrap i {
    font-size: 30px;
    color: #D97706;
}

.logout-title {
    font-size: 18px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 10px;
}

.logout-msg {
    font-size: 14px;
    color: #6B7280;
    line-height: 1.6;
    margin-bottom: 28px;
}

.logout-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.btn-logout-cancel {
    flex: 1;
    height: 42px;
    border: 1.5px solid #E5E7EB;
    border-radius: 12px;
    background: #fff;
    color: #374151;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background .15s;
}

.btn-logout-cancel:hover {
    background: #F3F4F6;
}

.btn-logout-confirm {
    flex: 1;
    height: 42px;
    border: none;
    border-radius: 12px;
    background: #D97706;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    transition: background .15s;
    width: 100%;
}

.btn-logout-confirm:hover {
    background: #B45309;
}

</style>

<script>

const sidebar   = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleSidebar');
const userBtn   = document.getElementById('userInfoBtn');

toggleBtn.addEventListener('click', () => {
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('show');
    } else {
        sidebar.classList.toggle('collapsed');
    }
});

// Klik avatar saat sidebar collapsed → buka sidebar, jangan navigate
userBtn.addEventListener('click', (e) => {
    if (sidebar.classList.contains('collapsed')) {
        e.preventDefault(); // cegah redirect dulu
        sidebar.classList.remove('collapsed');
    }
    // Kalau sidebar sudah terbuka → link berjalan normal ke /admin/profile
    
});

// Klik di luar sidebar → auto collapse/hide
document.addEventListener('click', (e) => {
    // Cek apakah klik di dalam sidebar atau tidak
    if (sidebar.contains(e.target)) return;

    if (window.innerWidth <= 768) {
        // Mobile: tutup sidebar
        sidebar.classList.remove('show');
    } else {
        // Desktop: collapse sidebar
        sidebar.classList.add('collapsed');
    }
});


</script>