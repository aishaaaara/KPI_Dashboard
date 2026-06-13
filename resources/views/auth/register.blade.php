<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>

<div class="register-wrapper">

    <div class="register-card">

        <div class="logo-section">

            <img src="{{ asset('images/cmlabs-logo.png') }}"
                 alt="Logo"
                 class="logo">

            <h2>Developer KPI Tracker</h2>

            <p>
                Kami akan mengirimkan permintaan register akun Anda
            </p>

        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('register.store') }}"
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

            <div class="mb-2">

                <label class="form-label">
                    Full Name
                </label>

                <input type="text"
                       name="name"
                       class="form-control custom-input"
                       placeholder="Masukkan Nama Lengkap"
                       required>

            </div>

            <div class="mb-2">

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

                    <i class="bi bi-eye-slash toggle-password"
                       data-target="password"></i>

                </div>

            </div>

            <div class="mb-4">

                <label class="form-label">
                    Confirm Password
                </label>

                <div class="password-box">

                    <input type="password"
                           name="password_confirmation"
                           id="confirm_password"
                           class="form-control custom-input"
                           placeholder="******"
                           required>

                    <i class="bi bi-eye-slash toggle-password"
                       data-target="confirm_password"></i>

                </div>

            </div>

            <button type="submit"
                    class="btn-register">

                Daftar

            </button>

        </form>

        <div class="signin-link">

            Sudah Memiliki Akun?

            <a href="{{ route('login') }}">
                Sign In
            </a>

        </div>

    </div>

</div>

<style>

body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#003A78;
}

.register-wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.register-card{
    width:480px;
    background:#fff;
    border-radius:16px;
    padding:35px 40px;
}

.logo-section{
    text-align:center;
    margin-bottom:25px;
}
.logo{
    width:180px;
    margin-bottom:12px;
}

.logo-section h2{
    font-size:16px;
    font-weight:700;
    margin-bottom:6px;
}

.logo-section p{
    font-size:12px;
    color:#98A2B3;
}

.form-label{
    font-size:14px;
    font-weight:600;
    margin-bottom:6px;
}

.custom-input{
    height:42px;
    border:none;
    border-radius:10px;
    background:#F2F4F7;
    padding:0 14px;
    font-size:14px;
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
    right:18px;
    transform:translateY(-50%);
    color:#98A2B3;
    cursor:pointer;
}

.btn-register{
    width:100%;
    height:44px;
    border:none;
    border-radius:10px;
    background:#2F9BF3;
    color:white;
    font-size:15px;
    font-weight:600;
}

.btn-register:hover{
    background:#2087dd;
}

.signin-link{
    text-align:center;
    margin-top:25px;
    color:#98A2B3;
    font-size:14px;
}

.signin-link a{
    color:#2F9BF3;
    text-decoration:none;
    font-weight:600;
    margin-left:4px;
}
.logo-section{
    margin-bottom:20px;
}

.signin-link{
    margin-top:18px;
    font-size:13px;
}

.password-box i{
    font-size:14px;
}
.custom-input::placeholder{
    color:#ddd8d8;
    opacity:1;
    font-size:14px;
}
</style>

<script>

document.querySelectorAll('.toggle-password')
.forEach(icon => {

    icon.addEventListener('click', function(){

        const target =
        document.getElementById(
            this.dataset.target
        );

        if(target.type === 'password'){
            target.type = 'text';
            this.classList.remove('bi-eye-slash');
            this.classList.add('bi-eye');
        }else{
            target.type = 'password';
            this.classList.remove('bi-eye');
            this.classList.add('bi-eye-slash');
        }

    });

});

</script>

</body>
</html>