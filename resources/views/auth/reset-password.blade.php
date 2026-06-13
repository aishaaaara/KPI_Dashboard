<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">

        {{-- LOGO --}}
        <div class="logo-section">
            <img src="{{ asset('images/cmlabs-logo.png') }}" alt="Logo" class="logo">
            <h2>Reset Password</h2>
            <p>Buat password baru untuk akun kamu.</p>
        </div>

        {{-- ALERT ERROR --}}
        @if (session('error'))
            <div class="alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- FORM --}}
        <form action="{{ route('password.reset') }}" method="POST">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            {{-- EMAIL (readonly, info saja) --}}
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input
                    type="email"
                    class="form-control custom-input"
                    value="{{ $email }}"
                    disabled>
            </div>

            {{-- PASSWORD --}}
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <div class="password-box">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control custom-input @error('password') is-invalid @enderror"
                        placeholder="Min. 6 karakter"
                        required>
                    <i class="bi bi-eye-slash toggle-password" data-target="password"></i>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- CONFIRM PASSWORD --}}
            <div class="mb-4">
                <label class="form-label">Konfirmasi Password</label>
                <div class="password-box">
                    <input
                        type="password"
                        name="password_confirmation"
                        id="confirmPassword"
                        class="form-control custom-input"
                        placeholder="Ulangi password baru"
                        required>
                    <i class="bi bi-eye-slash toggle-password" data-target="confirmPassword"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-lock"></i>
                Reset Password
            </button>

        </form>

        <div class="signin-link">
            Ingat password kamu?
            <a href="{{ route('login') }}">Sign In</a>
        </div>

    </div>
</div>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #003A78;
}

.auth-wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.auth-card {
    width: 420px;
    background: #fff;
    border-radius: 16px;
    padding: 35px 40px;
}

.logo-section {
    text-align: center;
    margin-bottom: 24px;
}

.logo {
    width: 180px;
    margin-bottom: 14px;
}

.logo-section h2 {
    font-size: 18px;
    font-weight: 700;
    color: #1F2340;
    margin: 0 0 6px;
}

.logo-section p {
    font-size: 13px;
    color: #98a2b3;
    margin: 0;
}

.form-label {
    font-size: 14px;
    font-weight: 600;
    color: #1F2340;
    margin-bottom: 6px;
}

.custom-input {
    height: 44px;
    border: none;
    border-radius: 10px;
    background: #F2F4F7;
    padding: 0 16px;
    font-size: 14px;
}

.custom-input:disabled {
    background: #e9ecef;
    color: #6b7280;
    cursor: not-allowed;
}

.custom-input::placeholder {
    color: #98A2B3;
    opacity: 1;
}

.custom-input:focus {
    background: #F2F4F7;
    box-shadow: none;
}

.password-box {
    position: relative;
}

.password-box i {
    position: absolute;
    top: 50%;
    right: 14px;
    transform: translateY(-50%);
    color: #98A2B3;
    font-size: 14px;
    cursor: pointer;
}

.btn-submit {
    width: 100%;
    height: 44px;
    border: none;
    border-radius: 10px;
    background: #2F9BF3;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: background .15s;
}

.btn-submit:hover {
    background: #2287dd;
}

.signin-link {
    margin-top: 18px;
    text-align: center;
    color: #98A2B3;
    font-size: 13px;
}

.signin-link a {
    color: #2F9BF3;
    text-decoration: none;
    font-weight: 600;
}

.alert-error {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13px;
    margin-bottom: 16px;
}
</style>

<script>
document.querySelectorAll('.toggle-password').forEach(icon => {
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

</body>
</html>