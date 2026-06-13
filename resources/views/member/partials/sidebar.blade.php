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
                <a href="/member/dashboard"
                   class="{{ request()->is('member/dashboard') ? 'active' : '' }}">

                    <i class="bi bi-grid-fill"></i>

                    <span>
                        Dashboard
                    </span>

                </a>
            </li>

            {{-- MEMBERS --}}
            <li>
                <a href="/member/members"
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
                <a href="/member/communication"
                   class="{{ request()->is('member/communication*') ? 'active' : '' }}">

                    <i class="bi bi-chat-left-text-fill"></i>

                    <span>
                        Communication
                    </span>

                </a>
            </li>

            {{-- STORY POINT --}}
            <li>
                <a href="/member/story-points"
                   class="{{ request()->is('member/story-points*') ? 'active' : '' }}">

                    <i class="bi bi-journal-text"></i>

                    <span>
                        Story Points
                    </span>

                </a>
            </li>

            {{-- WORKLOAD --}}
            <li>
                <a href="/member/workload"
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
                <a href="/member/performance-insight"
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
{{-- USER PROFILE --}}
<div class="sidebar-user">

    <a href="/member/profile" class="user-info" title="Lihat Profil">

        <div class="user-avatar">
            <i class="bi bi-person-circle"></i>
        </div>

        <div class="user-detail">
            <div class="user-name">{{ auth()->user()->name }}</div>
            <small>Member Access</small>
        </div>

    </a>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-btn border-0 bg-transparent" title="Logout">
            <i class="bi bi-box-arrow-right"></i>
        </button>
    </form>

</div>

</div>

<style>

.sidebar{
    width:260px;
    min-height:100vh;
    background:#082B5B;
    padding:24px 18px;
    color:white;
    position:sticky;
    top:0;
    transition:0.3s;
    overflow:hidden;
}

/* COLLAPSE */
.sidebar.collapsed{
    width:88px;
}

/* TOP */
.sidebar-top{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    margin-bottom:30px;
}

/* LOGO */
.logo-box{
    transition:0.3s;
}

.logo-box h4{
    font-size:18px;
    margin-bottom:4px;
    font-weight:700;
    white-space:nowrap;
}

.logo-box small{
    opacity:0.7;
    font-size:13px;
    white-space:nowrap;
}

/* HIDE TEXT */
.sidebar.collapsed .logo-text,
.sidebar.collapsed .logo-sub,
.sidebar.collapsed .menu-title,
.sidebar.collapsed .sidebar-menu span{
    display:none;
}

/* MENU TITLE */
.menu-title{
    font-size:12px;
    opacity:0.6;
    margin-bottom:10px;
    margin-top:24px;
    text-transform:uppercase;
    letter-spacing:1px;
    transition:0.3s;
}

/* MENU */
.sidebar-menu{
    list-style:none;
    padding:0;
    margin:0;
}

.sidebar-menu li{
    margin-bottom:10px;
}

.sidebar-menu a{
    text-decoration:none;
    color:white;
    display:flex;
    align-items:center;
    gap:14px;
    padding:12px 14px;
    border-radius:14px;
    transition:0.3s;
    font-size:14px;
    font-weight:500;
    white-space:nowrap;
}

/* CENTER ICON */
.sidebar.collapsed .sidebar-menu a{
    width:56px;
    height:56px;
    margin:auto;
    padding:0;
    justify-content:center;
    border-radius:18px;

}
/* ICON */
.sidebar.collapsed .sidebar-menu i{
    margin:0;
    min-width:auto;
    font-size:20px;
}

/* ACTIVE */
.sidebar.collapsed .sidebar-menu a.active{
    background:#0F4C9C;
    color:white;
}

/* SPACING */
.sidebar.collapsed .sidebar-menu li{
    margin-bottom:14px;
}

/* TOGGLE BUTTON */
.sidebar.collapsed .toggle-btn{
    margin:auto;
}
.sidebar.collapsed .sidebar-top{
    justify-content:center;
}
/* HOVER */
.sidebar-menu a:hover{
    background:#0F4C9C;
    color:white;
}

.sidebar-menu .active{
    background:#0F4C9C;
    color:white;
}

/* ICON */
.sidebar-menu i{
    font-size:18px;
    min-width:20px;
}

/* TOGGLE BUTTON */
.toggle-btn{
    width:40px;
    height:40px;
    border:none;
    border-radius:12px;
    background:#0F4C9C;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    cursor:pointer;
    transition:0.3s;
}

.toggle-btn:hover{
    background:#1a5db5;
}

/* MOBILE */
@media(max-width:768px){

    .sidebar{
        position:fixed;
        z-index:999;
        left:-260px;
    }

    .sidebar.show{
        left:0;
    }

}
/* PUSH USER TO BOTTOM */
.sidebar{
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}

/* USER CARD */
.sidebar-user {
    margin-top: 30px;
    background: white;
    border-radius: 12px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

/* USER INFO — sekarang jadi link */
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

.user-info:hover {
    background: #f3f4f6;
}

/* AVATAR */
.user-avatar {
    font-size: 34px;
    color: #6b7280;
    line-height: 1;
    flex-shrink: 0;
    transition: color .15s;
}

.user-info:hover .user-avatar {
    color: #2563eb;
}

/* DETAIL */
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

/* LOGOUT */
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

.sidebar.collapsed .sidebar-user {
    justify-content: center;
    padding: 10px;
}

.sidebar.collapsed .logout-btn { display: none; }

.sidebar.collapsed .user-info {
    margin: 0;
    padding: 0;
}
</style>

<script>

const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleSidebar');

toggleBtn.addEventListener('click', () => {

    if(window.innerWidth <= 768){

        sidebar.classList.toggle('show');

    }else{

        sidebar.classList.toggle('collapsed');

    }

});

</script>