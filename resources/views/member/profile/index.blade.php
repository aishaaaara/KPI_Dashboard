@extends('member.layouts.app')

@section('content')

{{-- ===================== HEADER ===================== --}}
<div class="profile-header">
    <div>
        <h2>My Profile</h2>
        <p>Informasi akun dan data kamu</p>
    </div>
</div>

{{-- ===================== ALERT ===================== --}}
@if (session('success'))
    <div class="alert-success-bar">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert-error-bar">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ session('error') }}
    </div>
@endif

<div class="profile-grid">

    {{-- ===================== CARD KIRI: INFO MEMBER ===================== --}}
    <div class="profile-card">

        {{-- Avatar --}}
        <div class="avatar-section">
            <div class="avatar-circle">
                @php
                    $nameParts = explode(' ', auth()->user()->name);
                    $initials  = strtoupper(
                        substr($nameParts[0], 0, 1) .
                        (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '')
                    );
                @endphp
                {{ $initials }}
            </div>
            <div class="avatar-info">
                <div class="avatar-name">{{ auth()->user()->name }}</div>
                <div class="avatar-role">
                    <span class="role-badge">Member</span>
                </div>
            </div>
        </div>

        <hr class="divider">

        {{-- Info List --}}
        <div class="info-list">

            <div class="info-item">
                <div class="info-label">
                    <i class="bi bi-person-badge"></i>
                    EID
                </div>
                <div class="info-value">{{ $member->eid ?? '-' }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">
                    <i class="bi bi-envelope"></i>
                    Email
                </div>
                <div class="info-value">{{ auth()->user()->email }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">
                    <i class="bi bi-briefcase"></i>
                    Posisi
                </div>
                <div class="info-value">{{ $member->position->name ?? '-' }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">
                    <i class="bi bi-people"></i>
                    Tim
                </div>
                <div class="info-value">{{ $member->team->name ?? '-' }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">
                    <i class="bi bi-file-earmark-person"></i>
                    Tipe Karyawan
                </div>
                <div class="info-value">{{ $member->employmentType->name ?? '-' }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">
                    <i class="bi bi-calendar-check"></i>
                    Tanggal Bergabung
                </div>
                <div class="info-value">
                    {{ $member->join_date ? \Carbon\Carbon::parse($member->join_date)->format('d M Y') : '-' }}
                </div>
            </div>

            @if ($member->end_date)
                <div class="info-item">
                    <div class="info-label">
                        <i class="bi bi-calendar-x"></i>
                        Tanggal Selesai
                    </div>
                    <div class="info-value">
                        {{ \Carbon\Carbon::parse($member->end_date)->format('d M Y') }}
                    </div>
                </div>
            @endif

        </div>

    </div>

    {{-- ===================== CARD KANAN: EDIT PROFIL ===================== --}}
    <div class="profile-card">

        <div class="card-title">
            <i class="bi bi-pencil-square"></i>
            Edit Profil
        </div>

        <form action="{{ route('member.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input
                    type="text"
                    name="name"
                    class="form-input @error('name') is-invalid @enderror"
                    value="{{ old('name', auth()->user()->name) }}"
                    required>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email (readonly) --}}
            <div class="form-group">
                <label class="form-label">Email</label>
                <input
                    type="email"
                    class="form-input"
                    value="{{ auth()->user()->email }}"
                    disabled>
                <small class="form-hint">Email tidak dapat diubah</small>
            </div>

            <hr class="divider">

            <div class="card-title" style="margin-bottom:16px">
                <i class="bi bi-lock"></i>
                Ganti Password
            </div>

            <small class="form-hint" style="display:block;margin-bottom:14px">
                Kosongkan jika tidak ingin mengganti password
            </small>

            {{-- Password baru --}}
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <div class="input-eye-wrap">
                    <input
                        type="password"
                        name="password"
                        id="newPassword"
                        class="form-input @error('password') is-invalid @enderror"
                        placeholder="Min. 6 karakter">
                    <i class="bi bi-eye-slash toggle-eye" data-target="newPassword"></i>
                </div>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Konfirmasi password --}}
            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <div class="input-eye-wrap">
                    <input
                        type="password"
                        name="password_confirmation"
                        id="confirmPassword"
                        class="form-input"
                        placeholder="Ulangi password baru">
                    <i class="bi bi-eye-slash toggle-eye" data-target="confirmPassword"></i>
                </div>
            </div>

            <button type="submit" class="btn-save">
                <i class="bi bi-check-lg"></i>
                Simpan Perubahan
            </button>

        </form>

    </div>

</div>

{{-- ===================== STYLES ===================== --}}
<style>

    /* ----- Header ----- */
    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .profile-header h2 {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .profile-header p {
        font-size: 13px;
        color: #98a2b3;
        margin: 4px 0 0;
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

    .alert-error-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        margin-bottom: 16px;
    }

    /* ----- Grid ----- */
    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 1.4fr;
        gap: 16px;
        align-items: start;
    }

    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ----- Card ----- */
    .profile-card {
        background: #fff;
        border: 1px solid #edf0f5;
        border-radius: 20px;
        padding: 24px;
    }

    /* ----- Avatar ----- */
    .avatar-section {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
    }

    .avatar-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #dbeafe;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .avatar-name {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }

    .role-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 11px;
        font-weight: 600;
    }

    /* ----- Divider ----- */
    .divider {
        border: none;
        border-top: 1px solid #f1f5f9;
        margin: 16px 0;
    }

    /* ----- Info List ----- */
    .info-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .info-label {
        font-size: 12px;
        color: #98a2b3;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
        width: 140px;
    }

    .info-label i {
        font-size: 13px;
    }

    .info-value {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        text-align: right;
    }

    /* ----- Card Title ----- */
    .card-title {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }

    .card-title i {
        color: #2563eb;
        font-size: 16px;
    }

    /* ----- Form ----- */
    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        display: block;
        margin-bottom: 6px;
    }

    .form-input {
        width: 100%;
        height: 42px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 0 14px;
        font-size: 13px;
        color: #374151;
        background: #f9fafb;
        transition: border-color .15s;
        box-sizing: border-box;
    }

    .form-input:focus {
        outline: none;
        border-color: #2563eb;
        background: #fff;
    }

    .form-input:disabled {
        background: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }

    .form-input.is-invalid {
        border-color: #ef4444;
    }

    .form-error {
        font-size: 11px;
        color: #ef4444;
        margin-top: 4px;
    }

    .form-hint {
        font-size: 11px;
        color: #b0b9c8;
        margin-top: 4px;
        display: block;
    }

    /* ----- Password eye ----- */
    .input-eye-wrap {
        position: relative;
    }

    .input-eye-wrap .form-input {
        padding-right: 40px;
    }

    .toggle-eye {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        color: #98a2b3;
        font-size: 14px;
        cursor: pointer;
    }

    /* ----- Save button ----- */
    .btn-save {
        width: 100%;
        height: 42px;
        border: none;
        border-radius: 12px;
        background: #2563eb;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        cursor: pointer;
        transition: background .15s;
        margin-top: 8px;
    }

    .btn-save:hover {
        background: #1d4ed8;
    }

</style>

{{-- ===================== SCRIPT ===================== --}}
<script>
    document.querySelectorAll('.toggle-eye').forEach(icon => {
        icon.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                input.type = 'password';
                this.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });
    });
</script>

@endsection