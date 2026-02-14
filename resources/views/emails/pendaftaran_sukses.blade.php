<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #000000; color: #ffffff; margin: 0; padding: 0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #000000; padding-bottom: 40px; }
        .main { background-color: #18181b; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; color: #ffffff; border-radius: 20px; border: 1px solid #27272a; overflow: hidden; }
        .header { background-color: #18181b; padding: 40px 0; text-align: center; }
        .header h1 { color: #f97316; font-size: 30px; font-weight: 900; font-style: italic; letter-spacing: -1px; margin: 0; text-transform: uppercase; }
        .content { padding: 30px; line-height: 1.6; }
        .h2 { font-size: 22px; font-weight: bold; text-align: center; margin-bottom: 20px; color: #ffffff; }
        .details-box { background-color: #000000; border: 1px solid #f97316; border-radius: 15px; padding: 20px; margin-bottom: 30px; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px solid #27272a; padding-bottom: 8px; }
        .label { color: #a1a1aa; font-size: 12px; text-transform: uppercase; font-weight: bold; }
        .value { color: #ffffff; font-weight: bold; font-size: 14px; }
        .footer { text-align: center; padding: 20px; font-size: 10px; color: #71717a; text-transform: uppercase; letter-spacing: 2px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main">
            <tr>
                <td class="header">
                    <h1>ARIFAH <span style="color: #ffffff;">GYM</span></h1>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <div class="h2">WELCOME TO THE TRIBE!</div>
                    <p>Halo <strong>{{ $member->name }}</strong>,</p>
                    <p>Pendaftaran Anda telah berhasil diproses. Selamat bergabung menjadi bagian dari komunitas ARIFAH Gym Makassar!</p>
                    
                    <div class="details-box">
                        <table width="100%">
                            <tr>
                                <td class="label">Order ID</td>
                                <td class="value" align="right">#{{ $member->order_id }}</td>
                            </tr>
                            <tr>
                                <td class="label">Paket Membership</td>
                                <td class="value" align="right">{{ $member->type == 'monthly' ? 'Bulanan (30 Hari)' : 'Harian' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Status</td>
                                <td class="value" align="right" style="color: #22c55e;">SUCCESS / ACTIVE</td>
                            </tr>
                        </table>
                    </div>

                    <p style="text-align: center; font-size: 14px; color: #a1a1aa;">
                        Silakan tunjukkan email ini atau bukti pembayaran Anda di meja resepsionis untuk memulai latihan perdana Anda.
                    </p>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    ARIFAH Gym Makassar • Power & Community • Est 2026
                </td>
            </tr>
        </table>
    </div>
</body>
</html>