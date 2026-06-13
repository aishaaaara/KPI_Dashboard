<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background:#f4f6f9; font-family:'Segoe UI',sans-serif;">

    <div style="max-width:480px; margin:40px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.08);">

        {{-- HEADER --}}
        <div style="background:#003A78; padding:32px 40px; text-align:center;">
            <h1 style="color:#fff; font-size:20px; font-weight:700; margin:0;">Dev KPI Tracker</h1>
            <p style="color:rgba(255,255,255,.7); font-size:13px; margin:6px 0 0;">Team Analytics</p>
        </div>

        {{-- BODY --}}
        <div style="padding:36px 40px;">

            <p style="font-size:15px; color:#1f2937; font-weight:600; margin:0 0 8px;">
                Halo, {{ $userName }}
            </p>

            <p style="font-size:14px; color:#6b7280; line-height:1.6; margin:0 0 24px;">
                Kami menerima permintaan reset password untuk akun kamu.
                Klik tombol di bawah untuk membuat password baru.
                Link ini hanya berlaku selama <strong>30 menit</strong>.
            </p>

            {{-- CTA BUTTON --}}
            <div style="text-align:center; margin-bottom:24px;">
                <a href="{{ $resetUrl }}"
                   style="display:inline-block; background:#2F9BF3; color:#fff; text-decoration:none;
                          padding:14px 32px; border-radius:10px; font-size:14px; font-weight:600;">
                    Reset Password
                </a>
            </div>

            <p style="font-size:12px; color:#98a2b3; line-height:1.6; margin:0 0 8px;">
                Jika tombol tidak berfungsi, salin link berikut ke browser kamu:
            </p>

            <p style="font-size:12px; color:#2F9BF3; word-break:break-all; margin:0 0 24px;">
                {{ $resetUrl }}
            </p>

            <hr style="border:none; border-top:1px solid #f1f5f9; margin:0 0 20px;">

            <p style="font-size:12px; color:#98a2b3; margin:0; line-height:1.6;">
                Jika kamu tidak merasa meminta reset password, abaikan email ini.
                Password kamu tidak akan berubah.
            </p>

        </div>

        {{-- FOOTER --}}
        <div style="background:#f8fafc; padding:16px 40px; text-align:center;">
            <p style="font-size:11px; color:#b0b9c8; margin:0;">
                Dev KPI Tracker. All rights reserved.
            </p>
        </div>

    </div>

</body>
</html>