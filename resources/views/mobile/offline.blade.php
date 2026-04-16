{{--
    Offline Page - PWA Mobile
    Displayed when user is offline and page is not cached
--}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0A66C2">
    <title>Offline - Bizmark Admin</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            padding-top: calc(24px + env(safe-area-inset-top));
            padding-bottom: calc(24px + env(safe-area-inset-bottom));
        }
        
        .offline-container {
            text-align: center;
            max-width: 320px;
        }
        
        .offline-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #0A66C2 0%, #004182 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 40px rgba(10, 102, 194, 0.3);
        }
        
        .offline-icon svg {
            width: 56px;
            height: 56px;
            fill: white;
        }
        
        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
        }
        
        p {
            font-size: 16px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        
        .retry-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            background: linear-gradient(135deg, #0A66C2 0%, #004182 100%);
            color: white;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(10, 102, 194, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
            -webkit-tap-highlight-color: transparent;
        }
        
        .retry-btn:active {
            transform: scale(0.97);
        }
        
        .retry-btn svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }
        
        .tips {
            margin-top: 40px;
            padding: 20px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        .tips h3 {
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .tips ul {
            list-style: none;
            text-align: left;
        }
        
        .tips li {
            font-size: 14px;
            color: #64748b;
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .tips li svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            fill: #0A66C2;
        }
        
        .status-indicator {
            position: fixed;
            bottom: calc(20px + env(safe-area-inset-bottom));
            left: 50%;
            transform: translateX(-50%);
            padding: 10px 20px;
            background: #ef4444;
            color: white;
            font-size: 13px;
            font-weight: 500;
            border-radius: 100px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(239, 68, 68, 0.3);
        }
        
        .status-indicator.online {
            background: #22c55e;
            box-shadow: 0 4px 16px rgba(34, 197, 94, 0.3);
        }
        
        .pulse {
            width: 8px;
            height: 8px;
            background: currentColor;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .spinning {
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="offline-icon">
            <svg viewBox="0 0 24 24">
                <path d="M23.64 7c-.45-.34-4.93-4-11.64-4-1.5 0-2.89.19-4.15.48L18.18 13.8 23.64 7zM17.04 15.22L3.27 1.44 2 2.72l2.05 2.06C1.91 5.76.59 6.82.36 7L12 21.5l3.07-3.93 4.2 4.2 1.27-1.27-3.5-3.28z"/>
            </svg>
        </div>
        
        <h1>Anda Sedang Offline</h1>
        <p>Tidak dapat terhubung ke server. Periksa koneksi internet Anda dan coba lagi.</p>
        
        <button class="retry-btn" onclick="retryConnection()">
            <svg id="retry-icon" viewBox="0 0 24 24">
                <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
            </svg>
            <span id="retry-text">Coba Lagi</span>
        </button>
        
        <div class="tips">
            <h3>Sementara waktu Anda bisa:</h3>
            <ul>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    Lihat halaman yang sudah di-cache
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    Data terakhir tersimpan otomatis
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    Data akan sync saat online
                </li>
            </ul>
        </div>
    </div>
    
    <div class="status-indicator" id="status">
        <div class="pulse"></div>
        <span>Offline</span>
    </div>
    
    <script>
        // Monitor online/offline status
        function updateStatus() {
            const statusEl = document.getElementById('status');
            if (navigator.onLine) {
                statusEl.innerHTML = '<div class="pulse"></div><span>Online - Redirecting...</span>';
                statusEl.classList.add('online');
                setTimeout(() => {
                    window.location.href = '/m';
                }, 1000);
            } else {
                statusEl.innerHTML = '<div class="pulse"></div><span>Offline</span>';
                statusEl.classList.remove('online');
            }
        }
        
        window.addEventListener('online', updateStatus);
        window.addEventListener('offline', updateStatus);
        
        // Retry connection
        function retryConnection() {
            const icon = document.getElementById('retry-icon');
            const text = document.getElementById('retry-text');
            
            icon.classList.add('spinning');
            text.textContent = 'Mencoba...';
            
            fetch('/m/dashboard/refresh', { 
                method: 'GET',
                cache: 'no-store'
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = '/m';
                } else {
                    throw new Error('Still offline');
                }
            })
            .catch(() => {
                icon.classList.remove('spinning');
                text.textContent = 'Masih Offline';
                setTimeout(() => {
                    text.textContent = 'Coba Lagi';
                }, 2000);
            });
        }
        
        // Check status on load
        updateStatus();
    </script>
</body>
</html>
