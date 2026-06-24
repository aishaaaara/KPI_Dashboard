<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f5f6fa; margin: 0; padding: 0; }
        .wrapper { max-width: 520px; margin: 40px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { padding: 32px; text-align: center; }
        .header-approved { background: #003A78; }
        .header-rejected { background: #7f1d1d; }
        .header h1 { color: #fff; font-size: 20px; margin: 0; }
        .body { padding: 32px; }
        .body p { color: #374151; font-size: 14px; line-height: 1.7; margin: 0 0 12px; }
        .status-box { border-radius: 12px; padding: 16px 20px; margin: 20px 0; }
        .status-approved { background: #e8fff1; border: 1px solid #bbf7d0; color: #16a34a; }
        .status-rejected { background: #fff1f1; border: 1px solid #fecaca; color: #dc2626; }
        .status-box p { margin: 0; font-weight: 600; font-size: 14px; }
        .btn { display: inline-block; margin-top: 20px; padding: 12px 24px; background: #2F9BF3; color: #fff; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px; }
        .footer { padding: 20px 32px; border-top: 1px solid #f1f5f9; text-align: center; }
        .footer p { color: #98a2b3; font-size: 12px; margin: 0; }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="header {{ $status === 'approved' ? 'header-approved' : 'header-rejected' }}">
        <h1>Developer KPI Tracker</h1>
    </div>

    <div class="body">

        <p>Halo, <strong>{{ $userName }}</strong>!</p>

        @if($status === 'approved')

            <p>Selamat! Permintaan registrasi akun Anda telah <strong>disetujui</strong> oleh admin.</p>

            <div class="status-box status-approved">
                <p>✅ Akun Anda sudah aktif dan siap digunakan.</p>
            </div>

            <p>Silakan login menggunakan email dan password yang Anda daftarkan.</p>

            <a href="{{ url('/login') }}" class="btn">Login Sekarang</a>

        @else

            <p>Mohon maaf, permintaan registrasi akun Anda telah <strong>ditolak</strong> oleh admin.</p>

            <div class="status-box status-rejected">
                <p>❌ Permintaan registrasi ditolak.</p>
            </div>

            @if($reason)
                <p>Alasan: <strong>{{ $reason }}</strong></p>
            @endif

            <p>Jika Anda merasa ini adalah kesalahan, silakan hubungi admin.</p>

        @endif

    </div>

    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem Developer KPI Tracker. Harap tidak membalas email ini.</p>
    </div>

</div>

</body>
</html>