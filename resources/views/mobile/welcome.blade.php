<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#6366f1">
    <title>Bizmark Mobile Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 20px;
        }
        
        .container {
            text-align: center;
            max-width: 400px;
        }
        
        .logo {
            font-size: 64px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        h1 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .btn {
            display: inline-block;
            background: white;
            color: #667eea;
            padding: 16px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .features {
            margin-top: 40px;
            text-align: left;
        }
        
        .feature {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 12px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        
        .feature-icon {
            font-size: 24px;
            margin-right: 15px;
            width: 40px;
            text-align: center;
        }
        
        .feature-text {
            font-size: 14px;
            flex: 1;
        }
        
        .error-message {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.5);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">📱</div>
        <h1>Bizmark Mobile</h1>
        <p>Kelola proyek, approve expenses, dan track tasks dari mana saja</p>
        
        <div class="error-message">
            ⚠️ <strong>Anda belum login!</strong><br>
            Silakan login terlebih dahulu untuk mengakses mobile admin.
        </div>
        
        <a href="{{ url('/hadez') }}" class="btn">Login Sekarang</a>
        
        <div class="features">
            <div class="feature">
                <div class="feature-icon">⚡</div>
                <div class="feature-text">
                    <strong>Quick Approvals</strong><br>
                    Approve expenses dalam 5 detik dengan swipe gesture
                </div>
            </div>
            
            <div class="feature">
                <div class="feature-icon">📊</div>
                <div class="feature-text">
                    <strong>Real-time Dashboard</strong><br>
                    Monitor proyek dan finansial secara real-time
                </div>
            </div>
            
            <div class="feature">
                <div class="feature-icon">📡</div>
                <div class="feature-text">
                    <strong>Works Offline</strong><br>
                    Tetap produktif bahkan tanpa koneksi internet
                </div>
            </div>
            
            <div class="feature">
                <div class="feature-icon">🔔</div>
                <div class="feature-text">
                    <strong>Push Notifications</strong><br>
                    Dapatkan notifikasi untuk approval penting
                </div>
            </div>
        </div>
    </div>
</body>
</html>
