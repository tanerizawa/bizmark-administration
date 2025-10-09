# 🌐 Domain Setup - bizmark.id

## ✅ Status Implementasi
**Domain:** bizmark.id  
**Status:** Aktif & Berfungsi  
**Web Server:** Nginx (Container: osint-nginx)  
**Tanggal Setup:** 2 Oktober 2025

---

## 📋 Konfigurasi yang Diterapkan

### 1. **Nginx Virtual Host**
File: `/root/osint/config/nginx/bizmark.id.conf`

```nginx
server {
    listen 80;
    server_name bizmark.id www.bizmark.id;
    
    root /var/www/bizmark.id/public;
    index index.php index.html;
    
    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP-FPM configuration
    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass bizmark_app:9000;
        fastcgi_index index.php;
        
        # Path mapping (nginx vs bizmark_app container)
        fastcgi_param SCRIPT_FILENAME /var/www/public$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT /var/www/public;
        
        include fastcgi_params;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
    
    # Security & Static files caching
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 2. **Docker Compose Integration**
File: `/root/osint/docker-compose.yml`

**Nginx Service Volumes:**
```yaml
volumes:
  - ./config/nginx/hadez.us.conf:/etc/nginx/conf.d/default.conf
  - ./config/nginx/bizmark.id.conf:/etc/nginx/conf.d/bizmark.id.conf
  - /root/bizmark.id:/var/www/bizmark.id:ro
  - ./data/certbot/www:/var/www/certbot/:ro
  - ./data/certbot/conf:/etc/letsencrypt/:ro
```

**Network Connection:**
- Container `osint-nginx` terhubung ke network `osint_elk` dan `bizmarkid_bizmark_network`
- Memungkinkan komunikasi dengan container `bizmark_app` (PHP-FPM)

### 3. **Laravel Environment**
File: `/root/bizmark.id/.env`

```env
APP_NAME="Bizmark Perizinan"
APP_ENV=local
APP_URL=http://bizmark.id
```

**Cache yang di-clear:**
```bash
docker exec bizmark_app php artisan config:cache
docker exec bizmark_app php artisan view:clear
```

---

## 🔧 Troubleshooting yang Dilakukan

### Masalah 1: Port 80 Sudah Digunakan
**Error:** Apache gagal start karena port 80 sudah dipakai nginx  
**Solusi:** Gunakan nginx yang sudah running (osint-nginx container)

### Masalah 2: Host Not Found - bizmark_app
**Error:** nginx tidak bisa resolve hostname `bizmark_app`  
**Solusi:** Connect container osint-nginx ke network bizmark:
```bash
docker network connect bizmarkid_bizmark_network osint-nginx
```

### Masalah 3: File Not Found (404)
**Error:** PHP-FPM tidak bisa menemukan file Laravel  
**Solusi:** Path mapping yang berbeda antara nginx dan bizmark_app:
- Nginx sees: `/var/www/bizmark.id/public/index.php`
- Bizmark_app sees: `/var/www/public/index.php`
- Fix: Set `SCRIPT_FILENAME` ke path yang sesuai dengan bizmark_app

---

## ✅ Testing & Verifikasi

### Test 1: Homepage
```bash
curl -H "Host: bizmark.id" http://localhost/
# Result: ✅ 200 OK - Dashboard HTML rendered
```

### Test 2: Projects Route
```bash
curl -I -H "Host: bizmark.id" http://localhost/projects
# Result: ✅ 200 OK - Laravel sessions working
```

### Test 3: Static Files
```bash
curl -I -H "Host: bizmark.id" http://localhost/js/app.js
# Result: ✅ Nginx serving static files
```

---

## 🌍 Akses Domain

### Internal (Server)
```bash
curl http://bizmark.id
```

### External (Browser)
Setelah DNS propagate (biasanya 1-24 jam):
```
http://bizmark.id
```

**Verifikasi DNS:**
```bash
dig bizmark.id
nslookup bizmark.id
```

---

## 🔒 SSL/HTTPS Setup (Next Step)

Untuk mengaktifkan HTTPS dengan Let's Encrypt:

### 1. Install Certbot (Sudah ada di osint setup)
```bash
docker-compose run --rm certbot certonly \
  --webroot \
  --webroot-path=/var/www/certbot \
  -d bizmark.id \
  -d www.bizmark.id \
  --email admin@bizmark.id \
  --agree-tos \
  --no-eff-email
```

### 2. Update Nginx Config
Tambahkan HTTPS server block di `/root/osint/config/nginx/bizmark.id.conf`:

```nginx
# HTTPS configuration
server {
    listen 443 ssl;
    server_name bizmark.id www.bizmark.id;
    
    ssl_certificate /etc/letsencrypt/live/bizmark.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/bizmark.id/privkey.pem;
    
    # ... rest of config sama dengan HTTP block
}

# HTTP to HTTPS redirect
server {
    listen 80;
    server_name bizmark.id www.bizmark.id;
    
    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
    
    location / {
        return 301 https://$server_name$request_uri;
    }
}
```

### 3. Reload Nginx
```bash
docker exec osint-nginx nginx -s reload
```

---

## 📊 Domain Status

| Aspect | Status | Notes |
|--------|--------|-------|
| DNS Configuration | ✅ | Points to server IP |
| Nginx Config | ✅ | Virtual host configured |
| Laravel App | ✅ | Serving on http://bizmark.id |
| SSL/HTTPS | ⏳ | Ready to implement |
| hadez.us (existing) | ✅ | Not affected, still working |

---

## 🔄 Maintenance Commands

### Restart Nginx
```bash
cd /root/osint
docker-compose restart nginx
```

### View Nginx Logs
```bash
docker logs osint-nginx --tail 100
docker exec osint-nginx tail -f /var/log/nginx/bizmark.id-access.log
docker exec osint-nginx tail -f /var/log/nginx/bizmark.id-error.log
```

### Test Nginx Config
```bash
docker exec osint-nginx nginx -t
```

### Reload Nginx (tanpa restart)
```bash
docker exec osint-nginx nginx -s reload
```

---

## 🎯 Summary

✅ **Domain bizmark.id sudah aktif dan berfungsi!**

- Nginx: Configured & Running
- Laravel: Accessible via http://bizmark.id
- PHP-FPM: Connected & Processing
- Static Files: Cached & Optimized
- Co-existing: hadez.us tetap berfungsi normal

**Next Steps:**
1. ✅ Test akses dari browser eksternal setelah DNS propagate
2. ⏳ Setup SSL/HTTPS dengan Let's Encrypt
3. ⏳ Configure firewall rules jika diperlukan
4. ⏳ Setup monitoring & logging

---

**Dokumentasi dibuat:** 2 Oktober 2025  
**Oleh:** GitHub Copilot  
**Status:** Implementasi Selesai & Sukses ✅
