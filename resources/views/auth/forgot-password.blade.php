```blade
<!DOCTYPE html>
<html>
<head>

    <title>Reset Password</title>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>

<div class="auth-wrapper">

    <div class="auth-card">

        <div class="logo-section">

            <img src="{{ asset('images/cmlabs-logo.png') }}"
                 alt="Logo"
                 class="logo">

            <h2>
                Reset Your Password
            </h2>

        </div>

        <form
            {{-- action="{{ route('password.update') }}"
            method="POST"> --}}

            @csrf

            <div class="mb-3">

                <label class="form-label">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    class="form-control custom-input"
                    placeholder="userguest@gmail.com"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Full Name
                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control custom-input"
                    placeholder="Nama Lengkap"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Password
                </label>

                <div class="password-box">

                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control custom-input"
                        placeholder="******"
                        required>

                    <i
                        class="bi bi-eye-slash toggle-password"
                        data-target="password">
                    </i>

                </div>

            </div>

            <div class="mb-4">

                <label class="form-label">
                    Confirm Password
                </label>

                <div class="password-box">

                    <input
                        type="password"
                        name="password_confirmation"
                        id="confirmPassword"
                        class="form-control custom-input"
                        placeholder="******"
                        required>

                    <i
                        class="bi bi-eye-slash toggle-password"
                        data-target="confirmPassword">
                    </i>

                </div>

            </div>

            <button
                type="submit"
                class="btn-submit">

                Reset Password

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

.auth-wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.auth-card{
    width:420px;
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
    margin-bottom:14px;
}

.logo-section h2{
    font-size:16px;
    font-weight:700;
    color:#1F2340;
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
    color:#98A2B3;
    opacity:1;
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
    color:#98A2B3;
    font-size:14px;
    cursor:pointer;
}

.btn-submit{
    width:100%;
    height:44px;
    border:none;
    border-radius:10px;
    background:#2F9BF3;
    color:#fff;
    font-size:15px;
    font-weight:600;
}

.btn-submit:hover{
    background:#2287dd;
}

.signin-link{
    margin-top:18px;
    text-align:center;
    color:#98A2B3;
    font-size:13px;
}

.signin-link a{
    color:#2F9BF3;
    text-decoration:none;
    font-weight:600;
}

</style>

<script>

document
.querySelectorAll('.toggle-password')
.forEach(icon => {

    icon.addEventListener('click', function(){

        const input =
        document.getElementById(
            this.dataset.target
        );

        if(input.type === 'password'){

            input.type = 'text';

            this.classList.remove(
                'bi-eye-slash'
            );

            this.classList.add(
                'bi-eye'
            );

        }else{

            input.type = 'password';

            this.classList.remove(
                'bi-eye'
            );

            this.classList.add(
                'bi-eye-slash'
            );
        }

    });

});

</script>

</body>
</html>

