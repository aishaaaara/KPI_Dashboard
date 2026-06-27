<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <title>KPI Dashboard</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="preload"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2"
      as="font" type="font/woff2" crossorigin>

    <style>
        body {
            visibility: hidden;
            margin: 0;
            background: #f5f6fa;
            font-family: 'Segoe UI', sans-serif;
        }
        body.ready {
            visibility: visible;
            transition: opacity 0.15s ease;
        }

        .wrapper {
            display: flex;
            align-items: flex-start;
        }

        .main-content {
            flex: 1;
            padding: 24px;
            min-width: 0;
            overflow: hidden;
        }

        /* ── GLOBAL ALERT ── */
        .alert-success-custom,
        .alert-error-custom {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
            transition: opacity .3s, transform .3s;
        }

        .alert-success-custom {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .alert-error-custom {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert-success-custom i,
        .alert-error-custom i {
            font-size: 15px;
            flex-shrink: 0;
        }

        .alert-msg {
            flex: 1;
        }

        .alert-close-btn {
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            opacity: .5;
            line-height: 1;
            padding: 0;
            flex-shrink: 0;
            transition: opacity .15s;
            color: inherit;
            margin-left: auto;
        }

        .alert-close-btn:hover { opacity: 1; }

        .alert-success-custom.hide,
        .alert-error-custom.hide {
            opacity: 0;
            transform: translateY(-6px);
        }
    </style>

</head>

<body>

    <div class="wrapper">

        {{-- SIDEBAR --}}
        @include('member.partials.sidebar')

        {{-- CONTENT --}}
        <div class="main-content">

            @if(session('success'))
        <div class="alert-success-custom" id="globalSuccessAlert">
            <i class="bi bi-check-circle-fill"></i>
            <span class="alert-msg">{{ session('success') }}</span>
            <button class="alert-close-btn" onclick="dismissGlobalAlert('success')">×</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error-custom" id="globalErrorAlert">
            <i class="bi bi-x-circle-fill"></i>
            <span class="alert-msg">{{ session('error') }}</span>
            <button class="alert-close-btn" onclick="dismissGlobalAlert('error')">×</button>
        </div>
    @endif
            @yield('content')

        </div>

    </div>

{{-- Bootstrap JS — hanya sekali, sebelum </body> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
        document.body.classList.add('ready');
    });

function dismissGlobalAlert(type = 'success') {
    const id = type === 'error' ? 'globalErrorAlert' : 'globalSuccessAlert';
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('hide');
    setTimeout(() => el.remove(), 300);
}

document.addEventListener('DOMContentLoaded', function () {
    const successEl = document.getElementById('globalSuccessAlert');
    const errorEl   = document.getElementById('globalErrorAlert');
    if (successEl) setTimeout(() => dismissGlobalAlert('success'), 5000);
    if (errorEl)   setTimeout(() => dismissGlobalAlert('error'), 5000);
});
</script>

</body>
</html>