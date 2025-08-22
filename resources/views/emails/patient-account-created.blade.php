<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akun Pasien Klinik Kecantikan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #f59e0b 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .welcome-text {
            font-size: 18px;
            color: #374151;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #f3f4f6;
            border-left: 4px solid #6366f1;
            padding: 15px;
            margin: 20px 0;
        }
        .credentials {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .warning {
            background-color: #fee2e2;
            border: 1px solid #dc2626;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #991b1b;
        }
        .footer {
            background-color: #f3f4f6;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background-color: #6366f1;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Klinik Kecantikan</h1>
            <p>Selamat Datang!</p>
        </div>
        
        <div class="content">
            <p class="welcome-text">
                Halo <strong>{{ $patient->nama_lengkap }}</strong>,
            </p>
            
            <p>
                Selamat! Akun pasien Anda di Klinik Kecantikan telah berhasil dibuat. 
                Sekarang Anda dapat menggunakan sistem online kami untuk:
            </p>
            
            <div class="info-box">
                <ul>
                    <li>Membuat reservasi treatment secara online</li>
                    <li>Melihat riwayat kunjungan dan treatment</li>
                    <li>Mengakses katalog layanan kami</li>
                    <li>Mengelola profil dan informasi pribadi</li>
                </ul>
            </div>
            
            <div class="credentials">
                <h3 style="margin-top: 0; color: #d97706;">Informasi Login Anda:</h3>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Password Sementara:</strong> {{ $tempPassword }}</p>
            </div>
            
            <div class="warning">
                <strong>Penting:</strong> Demi keamanan, mohon segera ganti password Anda setelah login pertama kali.
            </div>
            
            <div style="text-align: center;">
                <a href="{{ config('app.url') }}/login" class="btn">Login Sekarang</a>
            </div>
            
            <p>
                Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi kami 
                di <strong>{{ config('mail.from.address') }}</strong> atau datang langsung ke klinik.
            </p>
            
            <p>
                Terima kasih telah mempercayakan perawatan kecantikan Anda kepada kami!
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Klinik Kecantikan. Semua hak dilindungi.</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>
