# Dokumentasi Teknis - Sistem Administrasi Konsultan Perizinan

## Overview
Sistem administrasi pengelolaan pekerjaan untuk konsultan perizinan yang dibangun dengan Laravel 11, MySQL, dan Docker. Sistem ini mengelola alur pekerjaan dari penawaran hingga terbitnya SK izin dengan modul manajemen proyek, tugas, dokumen, dan institusi.

## Teknologi Stack
- **Backend**: Laravel 11 (PHP 8.2)
- **Database**: MySQL 8.0
- **Frontend**: Blade Templates + Tailwind CSS
- **Web Server**: Nginx
- **Container**: Docker & Docker Compose
- **Development**: PHPMyAdmin untuk database management

## Struktur Database

### Tabel Utama
1. **users** - Pengguna sistem (admin, staff, viewer)
2. **institutions** - Data institusi terkait (DLH, BPN, OSS, dll)
3. **project_statuses** - Status workflow proyek
4. **projects** - Data proyek perizinan
5. **tasks** - Tugas per proyek
6. **documents** - Dokumen terkait proyek/tugas
7. **project_logs** - Audit trail aktivitas proyek

### Relationship Diagram
```
Users ──┐
         ├── Projects ──┬── Tasks ──┬── Documents
         │              │           └── (file attachments)
         └── ProjectLogs │
                        ├── Documents
                        └── ProjectStatus
Institutions ───────────┘
```

## Setup Development

### Prerequisites
- Docker & Docker Compose
- Git
- Terminal/Command Line

### Installation
```bash
# Clone repository
git clone <repository-url>
cd bizmark.id

# Build dan jalankan containers
docker-compose build
docker-compose up -d

# Jalankan migration dan seeder
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# Set permissions (jika diperlukan)
docker-compose exec -u root app chown -R www:www /var/www/storage
docker-compose exec -u root app chmod -R 775 /var/www/storage
```

### Akses Aplikasi
- **Web Application**: http://localhost:8081
- **PHPMyAdmin**: http://localhost:8080
- **Database Direct**: localhost:3306

### Default Users
```
Admin:   admin@bizmark.id   / admin123
Manager: manager@bizmark.id / manager123
Staff:   siti@bizmark.id    / staff123
Staff:   ahmad@bizmark.id   / staff123
Staff:   maya@bizmark.id    / staff123
```

## Docker Commands

### Container Management
```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker-compose logs -f app

# Restart specific service
docker-compose restart app

# Access app container shell
docker-compose exec app bash

# Access database
docker-compose exec db mysql -u bizmark_user -p bizmark_db
```

### Laravel Commands
```bash
# Run migrations
docker-compose exec app php artisan migrate

# Fresh migration with seed
docker-compose exec app php artisan migrate:fresh --seed

# Generate application key
docker-compose exec app php artisan key:generate

# Clear caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear

# Run tests (when available)
docker-compose exec app php artisan test
```

## Environment Configuration

### File .env
Konfigurasi utama aplikasi:
```env
APP_NAME="Bizmark Perizinan"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8081

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=bizmark_db
DB_USERNAME=bizmark_user
DB_PASSWORD=bizmark_password
```

### Docker Compose Services
1. **app**: PHP-FPM container dengan Laravel
2. **web**: Nginx reverse proxy
3. **db**: MySQL database
4. **phpmyadmin**: Database administration interface

## Model Structure

### Project Model
```php
// Relationships
- belongsTo: ProjectStatus (current_status_id)
- belongsTo: User (assigned_user_id)
- belongsTo: Institution (primary_institution_id)
- hasMany: Task, Document, ProjectLog

// Key Attributes
- code (unique project identifier)
- client information (name, company, contact)
- permit_type, sub_permits
- dates (contract, target_completion, actual_completion)
- project_value, location
- flags (is_urgent, is_archived)
```

### Task Model
```php
// Relationships
- belongsTo: Project, User (assigned_user_id)
- hasMany: Document

// Key Attributes
- title, description, sop_notes
- status (todo, in_progress, done, blocked)
- priority (low, normal, high, urgent)
- dates (due_date, started_at, completed_at)
```

### Document Model
```php
// Relationships
- belongsTo: Project, Task, User (uploaded_by)
- belongsTo: Document (parent_document_id) // for versioning

// Key Attributes
- file management (name, path, size, mime_type)
- categorization and versioning
- access control (is_confidential, access_permissions)
- status tracking (draft, review, approved, submitted, final)
```

## Security Considerations

### Authentication & Authorization
- Role-based access (admin, staff, viewer)
- Password hashing dengan bcrypt
- Session management dengan Laravel default

### File Security
- Dokumen disimpan di storage/app (tidak accessible via web)
- Access control melalui aplikasi
- File validation untuk type dan size

### Database Security
- Foreign key constraints
- Input validation via Laravel requests
- SQL injection protection via Eloquent ORM

## Backup & Recovery

### Database Backup
```bash
# Manual backup
docker-compose exec db mysqldump -u bizmark_user -p bizmark_db > backup_$(date +%Y%m%d).sql

# Restore backup
docker-compose exec -i db mysql -u bizmark_user -p bizmark_db < backup_file.sql
```

### File Backup
```bash
# Backup storage folder
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/

# Backup entire application
tar -czf app_backup_$(date +%Y%m%d).tar.gz --exclude=vendor --exclude=node_modules .
```

## Performance Optimization

### Laravel Optimization
```bash
# Production optimizations
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app composer install --optimize-autoloader --no-dev
```

### Database Optimization
- Index pada kolom yang sering dicari (project code, client name)
- Soft deletes untuk data penting
- Query optimization dengan eager loading

## Troubleshooting

### Common Issues

1. **Permission Errors**
```bash
docker-compose exec -u root app chown -R www:www /var/www
docker-compose exec -u root app chmod -R 775 /var/www/storage
```

2. **Database Connection Issues**
```bash
# Check if database container is running
docker-compose ps db

# Check database logs
docker-compose logs db

# Reset database
docker-compose exec app php artisan migrate:fresh --seed
```

3. **File Upload Issues**
```bash
# Check storage permissions
ls -la storage/

# Clear application caches
docker-compose exec app php artisan cache:clear
```

### Logs Location
- **Laravel Logs**: `storage/logs/laravel.log`
- **Nginx Logs**: Check with `docker-compose logs web`
- **MySQL Logs**: Check with `docker-compose logs db`

## Development Workflow

### Adding New Features
1. Create migration: `php artisan make:migration`
2. Create model: `php artisan make:model`
3. Create controller: `php artisan make:controller`
4. Add routes in `routes/web.php`
5. Create views in `resources/views/`
6. Test functionality
7. Update documentation

### Best Practices
- Follow Laravel conventions (naming, structure)
- Use Eloquent relationships instead of manual joins
- Validate all input data
- Log important activities
- Write clear commit messages
- Test before deployment

## Deployment Notes

### Production Setup
- Set `APP_ENV=production` dan `APP_DEBUG=false`
- Use proper SSL certificates
- Configure backup automation
- Set up monitoring and alerting
- Use environment-specific configurations
- Enable Laravel optimization commands

### Scaling Considerations
- Database indexing untuk performa
- File storage ke cloud (S3, etc.) untuk scalability
- Load balancer untuk multiple app instances
- Redis untuk caching dan sessions
- Queue workers untuk background tasks

---

**Dokumentasi ini akan diupdate seiring perkembangan sistem.**