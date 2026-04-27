<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - {{ config('app.name') }}</title>

    <style>
        /* ---------- CSS STYLE ---------- */
        body {
            margin: 0;
            background-color: #f2f4f6;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #51545E;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f2f4f6;
            padding: 30px 0;
        }
        .email-content {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }
        .email-header {
            background-color: #007bff;
            text-align: center;
            padding: 25px 0 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .email-header img {
            height: 50px;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 40px 50px;
        }
        .email-body p {
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 13px;
            padding: 20px;
            border-top: 1px solid #e9ecef;
            background: #ffffff;
        }

        /* ---------- DARK MODE ---------- */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0f1114;
                color: #e1e1e1;
            }
            .email-content {
                background-color: #1a1d21;
                box-shadow: none;
            }
            .email-header {
                background-color: #1a1d21;
                border-bottom: 1px solid #2a2f33;
            }
            .email-body {
                color: #e1e1e1;
            }
            .btn {
                background-color: #0d6efd;
            }
            .btn:hover {
                background-color: #0b5ed7;
            }
            .footer {
                background-color: #1a1d21;
                border-top: 1px solid #2a2f33;
                color: #9ca3af;
            }
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 576px) {
            .email-body {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="email-header">
                <img src="{{ asset('po-content/uploads/' . getSetting('logo')) }}" alt="{{ config('app.name') }} Logo">
                <h2 style="margin: 0; font-size: 20px; font-weight: 600; color: #ffffff;">
                    {{ config('app.name') }}
                </h2>
            </div>

            <div class="email-body">
                <p>Halo <strong>{{ $user->name ?? 'Pengguna' }}</strong>,</p>

                <p>Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda.</p>

                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{ $url }}" class="btn">Atur Ulang Kata Sandi</a>
                </p>

                <p>Link ini hanya berlaku selama <strong>60 menit</strong>. Jika Anda tidak meminta reset kata sandi, abaikan email ini.</p>

                <p>Salam hangat,<br><strong>Tim {{ config('app.name') }}</strong></p>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Hak cipta dilindungi.
            </div>
        </div>
    </div>
</body>
</html>