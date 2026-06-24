<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

<div class="register-wrapper">

    <div class="register-card">

        <div class="logo-section">
            <img src="{{ asset('images/cmlabs-logo.png') }}" alt="Logo" class="logo">
            <h2>Developer KPI Tracker</h2>
            <p>Kami akan mengirimkan permintaan register akun Anda</p>
        </div>

        {{-- ERROR VALIDASI --}}
        @if($errors->any())
            <div class="alert-box alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="alert-box alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        {{-- ERROR SESSION --}}
        @if(session('error'))
            <div class="alert-box alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif
        
        @if(session('success'))
            <div class="alert-box alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        <form action="{{ route('register.store') }}" method="POST">

            @csrf

            <div class="mb-2">
                <label class="form-label">Email</label>
                <input type="email"
                       name="email"
                       class="form-control custom-input {{ $errors->has('email') ? 'input-error' : '' }}"
                       placeholder="userguest@gmail.com"
                       value="{{ old('email') }}"
                       required>
            </div>

            <div class="mb-2">
                <label class="form-label">Full Name</label>
                <input type="text"
                       name="name"
                       class="form-control custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                       placeholder="Masukkan Nama Lengkap"
                       value="{{ old('name') }}"
                       required>
            </div>

            <div class="mb-2">
                <label class="form-label">Password</label>
                <div class="password-box">
                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control custom-input {{ $errors->has('password') ? 'input-error' : '' }}"
                           placeholder="******"
                           required>
                    <i class="bi bi-eye-slash toggle-password" data-target="password"></i>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <div class="password-box">
                    <input type="password"
                           name="password_confirmation"
                           id="confirm_password"
                           class="form-control custom-input"
                           placeholder="******"
                           required>
                    <i class="bi bi-eye-slash toggle-password" data-target="confirm_password"></i>
                </div>
            </div>

            <button type="submit" class="btn-register">Daftar</button>

        </form>

        <div class="signin-link">
            Sudah Memiliki Akun?
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

.register-wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.register-card {
    width: 480px;
    background: #fff;
    border-radius: 16px;
    padding: 35px 40px;
}

.logo-section {
    text-align: center;
    margin-bottom: 20px;
}

.logo {
    width: 180px;
    margin-bottom: 12px;
}

.logo-section h2 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 6px;
}

.logo-section p {
    font-size: 12px;
    color: #98A2B3;
}

/* Alert Box */
.alert-box {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 14px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 16px;
    line-height: 1.6;
}

.alert-box i {
    font-size: 16px;
    flex-shrink: 0;
    margin-top: 1px;
}

.alert-success {
    background: #e8fff1;
    color: #16a34a;
    border: 1px solid #bbf7d0;
}

.alert-error {
    background: #fff1f1;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.form-label {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 6px;
}

.custom-input {
    height: 42px;
    border: none;
    border-radius: 10px;
    background: #F2F4F7;
    padding: 0 14px;
    font-size: 14px;
}

.custom-input:focus {
    background: #F2F4F7;
    box-shadow: none;
    border-color: transparent;
}

.input-error {
    border: 1px solid #ef4444 !important;
    background: #fff5f5 !important;
}

.custom-input::placeholder {
    color: #ddd8d8;
    opacity: 1;
    font-size: 14px;
}

.password-box {
    position: relative;
}

.password-box i {
    position: absolute;
    top: 50%;
    right: 18px;
    transform: translateY(-50%);
    color: #98A2B3;
    cursor: pointer;
    font-size: 14px;
}

.btn-register {
    width: 100%;
    height: 44px;
    border: none;
    border-radius: 10px;
    background: #2F9BF3;
    color: white;
    font-size: 15px;
    font-weight: 600;
}

.btn-register:hover {
    background: #2087dd;
}

.signin-link {
    text-align: center;
    margin-top: 18px;
    color: #98A2B3;
    font-size: 13px;
}

.signin-link a {
    color: #2F9BF3;
    text-decoration: none;
    font-weight: 600;
    margin-left: 4px;
}
</style>

<script>
document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', function () {
        const target = document.getElementById(this.dataset.target);
        if (target.type === 'password') {
            target.type = 'text';
            this.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            target.type = 'password';
            this.classList.replace('bi-eye', 'bi-eye-slash');
        }
    });
});
</script>

</body>
</html>