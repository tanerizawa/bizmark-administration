# Server Configuration

## Upload Limits

### Nginx Configuration
File: `/etc/nginx/sites-available/bizmark.id`

```nginx
server {
    server_name bizmark.id www.bizmark.id;
    client_max_body_size 50M;  # Maximum upload size
    ...
}
```

### PHP Configuration
File: `/etc/php/8.4/fpm/php.ini`

```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 128M
```

## Apply Changes

After modifying configuration files:

```bash
# Test Nginx configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm

# Check services status
sudo systemctl status nginx php8.4-fpm
```

## Troubleshooting

### 413 Request Entity Too Large
- Check `client_max_body_size` in Nginx config
- Check `upload_max_filesize` and `post_max_size` in PHP config
- Restart both services after changes

### Large File Import (CSV/Excel)
Current limits support up to **50MB** file uploads.

If you need larger files:
1. Increase `client_max_body_size` in Nginx
2. Increase `upload_max_filesize` and `post_max_size` in PHP
3. Increase `max_execution_time` if processing takes long
4. Consider increasing `memory_limit` for large datasets

## Last Updated
November 14, 2025
