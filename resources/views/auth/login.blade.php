<!DOCTYPE html>
<html>
<head>

    <title>Developer KPI Tracker</title>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>

<div class="login-wrapper">

<div class="login-card">

    <div class="logo-section">

        <img src="{{ asset('images/cmlabs-logo.png') }}"
             alt="Logo"
             class="logo">

        <h2>Developer KPI Tracker</h2>

        <p>
            Selamat Datang! Silahkan Masukkan Detail Akun Anda.
        </p>

    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('login.process') }}"
          method="POST">

        @csrf

        <div class="mb-2">

            <label class="form-label">
                Email
            </label>

            <input type="email"
                   name="email"
                   class="form-control custom-input"
                   placeholder="userguest@gmail.com"
                   required>

        </div>

        <div class="mb-3">

            <label class="form-label">
                Password
            </label>

            <div class="password-box">

                <input type="password"
                       name="password"
                       id="password"
                       class="form-control custom-input"
                       placeholder="******"
                       required>

                <i class="bi bi-eye-slash"
                   id="togglePassword"></i>

            </div>

        </div>

        <button type="submit"
                class="btn-login">
            Masuk
        </button>

    </form>

    <div class="extra-link">

        Belum Memiliki Akun?

    <a href="{{ route('register') }}">
        Sign Up
    </a>

    </div>

    <div class="forgot-link">

        <a href="{{ route('forgot.password') }}">
            Forgot Password
        </a>

    </div>

</div>

</div>

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#003A78;
    overflow:hidden;
}

.login-wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.login-card{
    width:440px;
    background:#FFFFFF;
    border-radius:16px;
    padding:32px 38px;
}

.logo-section{
    text-align:center;
    margin-bottom:22px;
}

.logo{
    width:180px;
    display:block;
    margin:0 auto 10px;
}

.logo-section h2{
    font-size:16px;
    font-weight:700;
    color:#1F2340;
    margin-bottom:6px;
}

.logo-section p{
    font-size:12px;
    color:#98A2B3;
    margin:0;
}

.form-label{
    font-size:14px;
    font-weight:600;
    color:#1F2340;
    margin-bottom:6px;
}

.custom-input{
    height:44px;
    border:none;
    border-radius:10px;
    background:#F2F4F7;
    padding:0 16px;
    font-size:14px;
}

.custom-input::placeholder{
    color:#ddd8d8;
}

.custom-input:focus{
    background:#F2F4F7;
    box-shadow:none;
}

.password-box{
    position:relative;
}

.password-box i{
    position:absolute;
    top:50%;
    right:14px;
    transform:translateY(-50%);
    font-size:14px;
    color:#98A2B3;
    cursor:pointer;
}

.btn-login{
    width:100%;
    height:44px;
    border:none;
    border-radius:10px;
    background:#2F9BF3;
    color:white;
    font-size:15px;
    font-weight:600;
}

.btn-login:hover{
    background:#2388dc;
}

.extra-link{
    margin-top:18px;
    text-align:center;
    color:#98A2B3;
    font-size:13px;
}

.extra-link a{
    color:#2F9BF3;
    font-weight:600;
    text-decoration:none;
}

.forgot-link{
    margin-top:6px;
    text-align:center;
}

.forgot-link a{
    color:#98A2B3;
    font-size:13px;
}
</style>

<script>

const togglePassword =
document.getElementById('togglePassword');

const password =
document.getElementById('password');

togglePassword.addEventListener(
'click',
function(){

    const type =
    password.getAttribute('type')
    === 'password'
    ? 'text'
    : 'password';

    password.setAttribute(
    'type',
    type
    );

    this.classList.toggle(
    'bi-eye'
    );

});

</script>

</body>
</html>