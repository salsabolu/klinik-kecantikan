<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Klinik Kecantikan</title>
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
        .btn {
            display: inline-block;
            background-color: #6366f1;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: bold;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Klinik Kecantikan</h1>
            <p>Reset Password</p>
        </div>
        
        <div class="content">
            <h2>Reset Password Anda</h2>
            
            <p>
                Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.
            </p>
            
            <div style="text-align: center;">
                <a href="{{ $url }}" class="btn">Reset Password</a>
            </div>
            
            <p>
                Link reset password ini akan kedaluwarsa dalam {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} menit.
            </p>
            
            <div class="warning">
                <strong>Penting:</strong> Jika Anda tidak meminta reset password, tidak ada tindakan lebih lanjut yang diperlukan. 
                Abaikan email ini dan password Anda akan tetap aman.
            </div>
            
            <p>
                Jika Anda mengalami kesulitan mengklik tombol "Reset Password", salin dan tempel URL berikut ke browser web Anda:
            </p>
            
            <p style="word-break: break-all; background-color: #f3f4f6; padding: 10px; border-radius: 4px;">
                {{ $url }}
            </p>
            
            <p>
                Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi kami.
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Klinik Kecantikan. Semua hak dilindungi.</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>
