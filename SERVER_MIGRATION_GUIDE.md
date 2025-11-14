# 🚀 Server Migration Guide - Bizmark.id

## 📋 Migration Overview

**Purpose:** Memindahkan aplikasi Bizmark.id dari server lama ke server baru tanpa data loss  
**Date:** November 13, 2025  
**Database Size:** ~552KB  
**Downtime Estimate:** 30-60 menit

---

## ✅ Pre-Migration Checklist

### Old Server (Current)
- [x] Database exported: `database/backups/bizmark_db_migration_YYYYMMDD_HHMMSS.sql`
- [x] All code committed to GitHub
- [x] .env documented (see below)
- [x] File uploads backed up (if any in `storage/app/public`)
- [x] SSL certificates documented
- [x] Domain DNS settings documented

### New Server (Target)
- [ ] Server provisioned (Ubuntu/Debian recommended)
- [ ] Root/sudo access confirmed
- [ ] Domain DNS updated to new IP
- [ ] Minimum requirements met (see below)

---

## 🖥️ Server Requirements

### Minimum Specs
- **OS:** Ubuntu 22.04 LTS / Debian 12+
- **RAM:** 4GB minimum (8GB recommended)
- **Storage:** 20GB minimum (50GB recommended)
- **PHP:** 8.2 or 8.3
- **Database:** PostgreSQL 15+
- **Web Server:** Nginx (recommended) or Apache

### Required PHP Extensions
```bash
php-fpm
php-pgsql
php-mbstring
php-xml
php-bcmath
php-curl
php-zip
php-gd
php-intl
php-redis (optional, for caching)
```

### System Packages
```bash
nginx
postgresql-15
composer
nodejs (v18+)
npm
git
supervisor (for queue workers)
certbot (for SSL)
```

---

## 📦 Step 1: Prepare Old Server

### 1.1 Final Database Backup

```bash
cd /root/Bizmark.id

# Export database dengan timestamp
docker exec nusantara-postgres-prod pg_dump -U admin -d bizmark_db \
  --clean --if-exists --create \
  > database/backups/bizmark_final_$(date +%Y%m%d_%H%M%S).sql

# Verify backup size
ls -lh database/backups/bizmark_final_*.sql

# Compress for faster transfer
gzip database/backups/bizmark_final_*.sql
```

### 1.2 Backup Uploaded Files

```bash
# If you have user uploads
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/app/public

# Verify
ls -lh storage_backup_*.tar.gz
```

### 1.3 Document Current Configuration

Save current .env settings (DO NOT commit to Git):
```bash
# Copy .env for reference
cp .env .env.production.backup

# Document important settings
cat > migration_env_notes.txt << 'EOF'
DATABASE SETTINGS:
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=bizmark_db
DB_USERNAME=admin
DB_PASSWORD=admin123

MAIL SETTINGS:
MAIL_MAILER=log (change to mailgun on new server)
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME=Bizmark.id

GOOGLE ANALYTICS:
GA4_MEASUREMENT_ID=G-STWBTLT978

OPENROUTER AI:
OPENROUTER_API_KEY=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
OPENROUTER_MODEL=x-ai/grok-4-fast

APP SETTINGS:
APP_NAME=Laravel
APP_ENV=production
APP_DEBUG=false
APP_URL=https://bizmark.id
EOF
```

### 1.4 Final Git Push

```bash
# Add all changes
git add .

# Commit with descriptive message
git commit -m "feat: Complete email system implementation with Mailgun support

- Added email inbox management (compose, reply, star, trash)
- Added email campaign system with subscriber targeting
- Added email template library with variable system
- Added email subscriber management with unsubscribe
- Added email settings UI with Mailgun/SMTP configuration
- Added EmailInboxController, EmailCampaignController, EmailSubscriberController, EmailTemplateController, EmailSettingsController
- Added EmailInbox, EmailCampaign, EmailSubscriber, EmailTemplate, EmailCampaignSubscriber models
- Added email migrations and seeders
- Added setup-mailgun.sh automated setup script
- Added EMAIL_SYSTEM_COMPLETE.md documentation
- Added EMAIL_SERVER_IMPLEMENTATION_ANALYSIS.md (Mailgun vs self-hosted)
- Added SERVER_MIGRATION_GUIDE.md for server migration
- Updated sidebar with Email Management menu
- Updated routes with email management routes
- Database backup included in database/backups/
- Ready for production deployment"

# Push to GitHub
git push origin main

# Verify push success
git log --oneline -5
```

---

## 🆕 Step 2: Setup New Server

### 2.1 Initial Server Setup

```bash
# Update system
apt update && apt upgrade -y

# Install required packages
apt install -y nginx postgresql-15 postgresql-contrib \
  php8.3-fpm php8.3-pgsql php8.3-mbstring php8.3-xml \
  php8.3-bcmath php8.3-curl php8.3-zip php8.3-gd \
  php8.3-intl php8.3-cli php8.3-common \
  git curl unzip supervisor certbot python3-certbot-nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Install Node.js 18+
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# Verify installations
php -v
composer -V
node -v
npm -v
psql --version
nginx -v
```

### 2.2 Create Application User

```bash
# Create dedicated user for Laravel
adduser --disabled-password --gecos "" bizmark
usermod -aG www-data bizmark

# Setup home directory
su - bizmark
cd ~
```

### 2.3 Clone Repository

```bash
# As bizmark user
cd /home/bizmark

# Clone from GitHub
git clone https://github.com/tanerizawa/bizmark-administration.git bizmark.id
cd bizmark.id

# Verify email system files
ls -la app/Http/Controllers/Admin/Email*
ls -la app/Models/Email*
ls -la resources/views/admin/email/
```

### 2.4 Install Dependencies

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install

# Build assets
npm run build

# Set permissions
sudo chown -R bizmark:www-data /home/bizmark/bizmark.id
sudo chmod -R 775 /home/bizmark/bizmark.id/storage
sudo chmod -R 775 /home/bizmark/bizmark.id/bootstrap/cache
```

---

## 🗄️ Step 3: Setup Database

### 3.1 Create PostgreSQL Database

```bash
# Switch to postgres user
sudo su - postgres

# Create database and user
psql << EOF
CREATE DATABASE bizmark_db;
CREATE USER admin WITH ENCRYPTED PASSWORD 'your_new_secure_password';
GRANT ALL PRIVILEGES ON DATABASE bizmark_db TO admin;
ALTER DATABASE bizmark_db OWNER TO admin;
\q
EOF

# Exit postgres user
exit
```

### 3.2 Configure PostgreSQL

```bash
# Edit PostgreSQL config for remote access (if needed)
sudo nano /etc/postgresql/15/main/postgresql.conf

# Find and update:
# listen_addresses = 'localhost'

# Edit pg_hba.conf
sudo nano /etc/postgresql/15/main/pg_hba.conf

# Add:
# local   all             admin                                   md5
# host    all             admin           127.0.0.1/32            md5

# Restart PostgreSQL
sudo systemctl restart postgresql
sudo systemctl enable postgresql
```

### 3.3 Import Database

```bash
# Transfer database backup from old server
# Method 1: Using scp
scp root@OLD_SERVER_IP:/root/Bizmark.id/database/backups/bizmark_final_*.sql.gz .

# Method 2: Using rsync
rsync -avz root@OLD_SERVER_IP:/root/Bizmark.id/database/backups/bizmark_final_*.sql.gz .

# Method 3: Download from GitHub release (if uploaded)
# wget https://github.com/tanerizawa/bizmark-administration/releases/download/v1.0/database.sql.gz

# Decompress
gunzip bizmark_final_*.sql.gz

# Import to PostgreSQL
sudo su - postgres
psql bizmark_db < /home/bizmark/bizmark_final_*.sql

# Verify import
psql bizmark_db -c "\dt"
psql bizmark_db -c "SELECT COUNT(*) FROM users;"
psql bizmark_db -c "SELECT COUNT(*) FROM email_subscribers;"
psql bizmark_db -c "SELECT COUNT(*) FROM email_templates;"

# Exit postgres user
exit
```

### 3.4 Verify Email System Tables

```bash
# Check if all email tables exist
sudo su - postgres
psql bizmark_db << EOF
\dt email*
SELECT table_name FROM information_schema.tables 
WHERE table_name LIKE 'email%';
EOF
exit
```

Expected tables:
- email_inbox
- email_campaigns
- email_subscribers
- email_templates
- email_campaign_subscriber

---

## ⚙️ Step 4: Configure Application

### 4.1 Create .env File

```bash
cd /home/bizmark/bizmark.id

# Copy from example
cp .env.example .env

# Edit .env
nano .env
```

Update with these values:
```env
APP_NAME=Laravel
APP_ENV=production
APP_KEY=  # Will generate in next step
APP_DEBUG=false
APP_URL=https://bizmark.id

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=bizmark_db
DB_USERNAME=admin
DB_PASSWORD=your_new_secure_password

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail (keep as log first, configure Mailgun later)
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@bizmark.id"
MAIL_FROM_NAME="Bizmark.id"

# Google Analytics
GA4_MEASUREMENT_ID=G-STWBTLT978

# OpenRouter AI
OPENROUTER_API_KEY=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
OPENROUTER_MODEL=x-ai/grok-4-fast
```

### 4.2 Generate Application Key

```bash
php artisan key:generate

# Verify .env updated
grep APP_KEY .env
```

### 4.3 Run Migrations (if needed)

```bash
# Only if importing fresh database without structure
# php artisan migrate --force

# Link storage
php artisan storage:link

# Clear and cache config
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize
php artisan optimize
```

### 4.4 Set Permissions

```bash
sudo chown -R bizmark:www-data /home/bizmark/bizmark.id
sudo chmod -R 775 /home/bizmark/bizmark.id/storage
sudo chmod -R 775 /home/bizmark/bizmark.id/bootstrap/cache
sudo chmod 644 /home/bizmark/bizmark.id/.env
```

---

## 🌐 Step 5: Configure Web Server

### 5.1 Nginx Configuration

```bash
# Create Nginx config
sudo nano /etc/nginx/sites-available/bizmark.id
```

Add this configuration:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name bizmark.id www.bizmark.id;
    
    root /home/bizmark/bizmark.id/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Logging
    access_log /var/log/nginx/bizmark_access.log;
    error_log /var/log/nginx/bizmark_error.log;

    # PHP-FPM
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to sensitive files
    location ~ /\.(env|git|gitignore) {
        deny all;
        return 404;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/bizmark.id /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
sudo systemctl enable nginx
```

### 5.2 Configure PHP-FPM

```bash
# Edit PHP-FPM pool config
sudo nano /etc/php/8.3/fpm/pool.d/bizmark.conf
```

Add:
```ini
[bizmark]
user = bizmark
group = www-data
listen = /var/run/php/php8.3-fpm-bizmark.sock
listen.owner = bizmark
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

php_admin_value[upload_max_filesize] = 50M
php_admin_value[post_max_size] = 50M
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 300
```

```bash
# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
sudo systemctl enable php8.3-fpm
```

---

## 🔒 Step 6: SSL Certificate

### 6.1 Install SSL with Certbot

```bash
# Get SSL certificate
sudo certbot --nginx -d bizmark.id -d www.bizmark.id

# Follow prompts:
# - Enter email for urgent renewal notices
# - Agree to Terms of Service
# - Choose redirect HTTP to HTTPS (option 2)

# Test auto-renewal
sudo certbot renew --dry-run

# Check certificate
sudo certbot certificates
```

### 6.2 Verify HTTPS

```bash
# Test site
curl -I https://bizmark.id

# Should return HTTP/2 200
```

---

## 📧 Step 7: Configure Email System

### 7.1 Setup Mailgun (Recommended)

```bash
cd /home/bizmark/bizmark.id

# Run setup script
sudo ./setup-mailgun.sh

# Or manually configure:
composer require mailgun/mailgun-php symfony/mailgun-mailer

# Update .env
nano .env
```

Add Mailgun settings:
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.bizmark.id
MAILGUN_SECRET=your-mailgun-api-key
MAILGUN_ENDPOINT=api.eu.mailgun.net
MAIL_FROM_ADDRESS=noreply@bizmark.id
MAIL_FROM_NAME="Bizmark.id"
```

### 7.2 Configure DNS for Email

Add these DNS records at your domain provider:
```dns
# SPF Record
bizmark.id.     TXT  "v=spf1 include:mailgun.org ~all"

# DKIM Record (from Mailgun dashboard)
pic._domainkey.bizmark.id.   TXT   "k=rsa; p=YOUR_KEY..."

# Tracking domain
email.bizmark.id.   CNAME   mailgun.org.

# MX Records (optional, for receiving)
bizmark.id.     MX  10  mxa.eu.mailgun.org.
bizmark.id.     MX  10  mxb.eu.mailgun.org.
```

### 7.3 Test Email Sending

```bash
# Test via artisan
php artisan tinker

# In Tinker:
Mail::raw('Test from new server', function ($m) {
    $m->to('your-email@example.com')->subject('Migration Test');
});

# Or test via UI:
# Go to https://bizmark.id/hadez (login)
# Navigate to Email Settings
# Send test email
```

---

## 🔄 Step 8: Setup Queue Worker

### 8.1 Configure Supervisor

```bash
# Create supervisor config
sudo nano /etc/supervisor/conf.d/bizmark-worker.conf
```

Add:
```ini
[program:bizmark-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/bizmark/bizmark.id/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=bizmark
numprocs=2
redirect_stderr=true
stdout_logfile=/home/bizmark/bizmark.id/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Update supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start bizmark-worker:*

# Check status
sudo supervisorctl status
```

---

## ✅ Step 9: Verification & Testing

### 9.1 Application Health Check

```bash
# Check if application responds
curl -I https://bizmark.id

# Check login page
curl -I https://bizmark.id/hadez

# Check admin routes
curl -I https://bizmark.id/dashboard

# Check email routes
curl -I https://bizmark.id/admin/inbox
curl -I https://bizmark.id/admin/campaigns
curl -I https://bizmark.id/admin/subscribers
curl -I https://bizmark.id/admin/templates
curl -I https://bizmark.id/admin/email/settings
```

### 9.2 Database Verification

```bash
sudo su - postgres
psql bizmark_db << EOF
-- Check tables
\dt

-- Check users
SELECT id, name, email, created_at FROM users LIMIT 5;

-- Check email subscribers
SELECT COUNT(*) as total_subscribers FROM email_subscribers;

-- Check email templates
SELECT id, name, category FROM email_templates;

-- Check email campaigns
SELECT id, name, status, created_at FROM email_campaigns;

-- Check recent inbox
SELECT id, subject, created_at FROM email_inbox ORDER BY created_at DESC LIMIT 5;
EOF
exit
```

### 9.3 Email System Test

**Test Checklist:**
- [ ] Login to `/hadez` works
- [ ] Dashboard loads
- [ ] Navigate to Email Management menu
- [ ] Open Inbox - shows list
- [ ] Open Campaigns - shows list
- [ ] Open Subscribers - shows count
- [ ] Open Templates - shows list
- [ ] Open Email Settings
- [ ] Configure Mailgun credentials
- [ ] Send test email - received successfully
- [ ] Create new campaign
- [ ] Send campaign to test subscriber
- [ ] Check campaign opens in Mailgun dashboard

### 9.4 Performance Test

```bash
# Check response times
time curl -s https://bizmark.id > /dev/null

# Check memory usage
ps aux | grep php

# Check disk usage
df -h

# Check database connections
sudo su - postgres
psql bizmark_db -c "SELECT count(*) FROM pg_stat_activity;"
exit
```

---

## 🔍 Step 10: Monitor & Optimize

### 10.1 Setup Log Rotation

```bash
sudo nano /etc/logrotate.d/bizmark
```

Add:
```
/home/bizmark/bizmark.id/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    missingok
    create 0644 bizmark www-data
}
```

### 10.2 Setup Monitoring

```bash
# Install monitoring tools
sudo apt install htop iotop nethogs

# Check system resources
htop

# Monitor logs
tail -f /home/bizmark/bizmark.id/storage/logs/laravel.log
tail -f /var/log/nginx/bizmark_access.log
tail -f /var/log/nginx/bizmark_error.log
```

### 10.3 Setup Backups

```bash
# Create backup script
sudo nano /usr/local/bin/bizmark-backup.sh
```

Add:
```bash
#!/bin/bash
BACKUP_DIR="/home/bizmark/backups"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup database
sudo su - postgres -c "pg_dump bizmark_db" | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup application files
tar -czf $BACKUP_DIR/app_$DATE.tar.gz \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='storage/logs' \
    --exclude='storage/framework/cache' \
    /home/bizmark/bizmark.id

# Keep only last 7 days
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/bizmark-backup.sh

# Setup cron job
sudo crontab -e

# Add daily backup at 2 AM
0 2 * * * /usr/local/bin/bizmark-backup.sh >> /var/log/bizmark-backup.log 2>&1
```

---

## 🚨 Troubleshooting

### Issue: 500 Internal Server Error

**Check:**
```bash
# Laravel logs
tail -50 /home/bizmark/bizmark.id/storage/logs/laravel.log

# Nginx logs
tail -50 /var/log/nginx/bizmark_error.log

# PHP-FPM logs
tail -50 /var/log/php8.3-fpm.log

# Common fixes:
sudo chown -R bizmark:www-data /home/bizmark/bizmark.id/storage
sudo chmod -R 775 /home/bizmark/bizmark.id/storage
php artisan config:clear
php artisan cache:clear
```

### Issue: Database Connection Failed

**Check:**
```bash
# Test PostgreSQL connection
psql -U admin -h 127.0.0.1 -d bizmark_db

# Check if service running
sudo systemctl status postgresql

# Verify credentials in .env
cat .env | grep DB_

# Check pg_hba.conf
sudo cat /etc/postgresql/15/main/pg_hba.conf | grep admin
```

### Issue: Email Not Sending

**Check:**
```bash
# Verify Mailgun config
cat .env | grep MAIL
cat .env | grep MAILGUN

# Test directly
php artisan tinker
Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));

# Check queue
php artisan queue:failed

# Check Mailgun logs
# Login to Mailgun dashboard → Logs
```

### Issue: Permission Denied

**Fix:**
```bash
# Reset all permissions
sudo chown -R bizmark:www-data /home/bizmark/bizmark.id
sudo chmod -R 755 /home/bizmark/bizmark.id
sudo chmod -R 775 /home/bizmark/bizmark.id/storage
sudo chmod -R 775 /home/bizmark/bizmark.id/bootstrap/cache
sudo chmod 644 /home/bizmark/bizmark.id/.env
```

---

## 📊 Migration Checklist

### Pre-Migration
- [x] Database exported from old server
- [x] Code pushed to GitHub
- [x] .env settings documented
- [x] DNS records documented
- [x] SSL certificates noted

### Migration
- [ ] New server provisioned
- [ ] All dependencies installed
- [ ] Repository cloned
- [ ] Database imported
- [ ] .env configured
- [ ] Application key generated
- [ ] Permissions set correctly
- [ ] Nginx configured
- [ ] SSL installed
- [ ] Email system configured (Mailgun)
- [ ] Queue worker running
- [ ] Backups scheduled

### Post-Migration
- [ ] All URLs respond correctly
- [ ] Login works
- [ ] Dashboard loads
- [ ] Email system functional
  - [ ] Inbox accessible
  - [ ] Campaigns accessible
  - [ ] Subscribers visible
  - [ ] Templates accessible
  - [ ] Settings page loads
  - [ ] Test email sent successfully
- [ ] Database queries working
- [ ] Performance acceptable
- [ ] Logs monitored
- [ ] DNS fully propagated (24-48 hours)

### Cleanup Old Server
- [ ] Keep old server running 7 days
- [ ] Monitor for any missed data
- [ ] Final backup before shutdown
- [ ] Update documentation
- [ ] Archive old server config

---

## 📝 Important Notes

### Database Migration
- Database export includes **all email system tables**: email_inbox, email_campaigns, email_subscribers, email_templates, email_campaign_subscriber
- All user data, projects, tasks, documents preserved
- Email system ready to use immediately after import

### Email System Features
- ✅ Inbox management (compose, reply, star, trash)
- ✅ Campaign management with targeting
- ✅ Subscriber management with unsubscribe
- ✅ Template library with variables
- ✅ Email settings UI (Mailgun/SMTP)
- ✅ Newsletter subscription form on landing page
- ✅ Automated setup script (`setup-mailgun.sh`)

### Post-Migration Configuration
1. **Mailgun Setup** (if not using log driver):
   - Sign up at mailgun.com
   - Verify domain mg.bizmark.id
   - Add DNS records
   - Run `./setup-mailgun.sh` or configure via UI
   
2. **Queue Worker**: Already configured with Supervisor for background email sending

3. **Monitoring**: Check logs daily for first week

### Support Resources
- Email System Docs: `EMAIL_SYSTEM_COMPLETE.md`
- Email Server Analysis: `EMAIL_SERVER_IMPLEMENTATION_ANALYSIS.md`
- Laravel Docs: https://laravel.com/docs
- Mailgun Docs: https://documentation.mailgun.com/

---

## 🎯 Success Criteria

Migration is successful when:
- ✅ Application loads at https://bizmark.id
- ✅ Login works with existing credentials
- ✅ All dashboard features functional
- ✅ Email system fully operational
  - ✅ Can view inbox
  - ✅ Can create campaigns
  - ✅ Can manage subscribers
  - ✅ Can edit templates
  - ✅ Can send test email successfully
- ✅ Database queries return correct data
- ✅ No errors in Laravel logs
- ✅ Response time < 1 second
- ✅ SSL certificate valid
- ✅ All DNS records propagated

---

## 📞 Emergency Contacts

If issues arise:
1. Check logs: `/home/bizmark/bizmark.id/storage/logs/laravel.log`
2. Check database: `psql bizmark_db`
3. Check services: `sudo systemctl status nginx postgresql php8.3-fpm`
4. Rollback: Keep old server accessible for 7 days

---

**Migration prepared by:** GitHub Copilot  
**Date:** November 13, 2025  
**Version:** 1.0  
**Status:** Ready for execution
