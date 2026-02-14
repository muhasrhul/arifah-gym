<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ARIFAH Gym</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 42px;
            font-weight: 900;
            font-style: italic;
            color: #ffffff;
            letter-spacing: -1px;
        }
        .header .brand-accent {
            color: #0992C2;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 11px;
            color: #888888;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: bold;
        }
        .content {
            padding: 40px 30px;
            color: #333333;
        }
        .icon-box {
            width: 80px;
            height: 80px;
            background-color: rgba(9, 146, 194, 0.1);
            border-radius: 50%;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
        .content h2 {
            margin: 0 0 20px 0;
            font-size: 24px;
            font-weight: 900;
            text-transform: uppercase;
            color: #000000;
            text-align: center;
            letter-spacing: -0.5px;
        }
        .content p {
            margin: 0 0 20px 0;
            font-size: 15px;
            line-height: 1.8;
            color: #555555;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .reset-button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #0992C2 0%, #0992C2 100%);
            color: #000000 !important;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 900;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(9, 146, 194, 0.3);
            transition: all 0.3s ease;
        }
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(9, 146, 194, 0.4);
        }
        .info-box {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 6px;
        }
        .info-box p {
            margin: 0;
            font-size: 13px;
            color: #856404;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #eeeeee;
        }
        .footer p {
            margin: 5px 0;
            font-size: 12px;
            color: #888888;
        }
        .footer .copyright {
            margin-top: 20px;
            font-size: 11px;
            color: #aaaaaa;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
        }
        .divider {
            height: 1px;
            background-color: #eeeeee;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>ARIFAH <span class="brand-accent">GYM</span></h1>
            <p>Admin Panel</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="icon-box">
                🔑
            </div>
            
            <h2>Reset Password</h2>
            
            <p>Halo Admin,</p>
            
            <p>Kami menerima permintaan untuk mereset password akun admin Anda di sistem ARIFAH Gym. Klik tombol di bawah ini untuk membuat password baru:</p>
            
            <div class="button-container">
                <a href="{{ url('reset-password/' . $token . '?email=' . urlencode($email)) }}" class="reset-button">
                    Reset Password Sekarang
                </a>
            </div>
            
            <div class="info-box">
                <p><strong>⚠️ Penting:</strong> Link reset password ini hanya berlaku selama <strong>24 jam</strong>. Setelah itu, Anda harus meminta link baru.</p>
            </div>
            
            <div class="divider"></div>
            
            <p style="font-size: 13px; color: #888888;">
                Jika tombol di atas tidak berfungsi, copy dan paste link berikut ke browser Anda:
            </p>
            <p style="font-size: 12px; color: #0992C2; word-break: break-all;">
                {{ url('reset-password/' . $token . '?email=' . urlencode($email)) }}
            </p>
            
            <div class="divider"></div>
            
            <p style="font-size: 13px; color: #888888;">
                <strong>Tidak meminta reset password?</strong><br>
                Abaikan email ini. Password Anda tetap aman dan tidak akan berubah.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>ARIFAH Gym Management System</strong></p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas.</p>
            <p class="copyright">ARIFAH Gym &copy; 2026</p>
        </div>
    </div>
</body>
</html>
