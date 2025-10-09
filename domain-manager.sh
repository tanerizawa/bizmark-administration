#!/bin/bash
# Quick Domain Management Script for bizmark.id
# Usage: ./domain-manager.sh [command]

NGINX_CONTAINER="osint-nginx"
APP_CONTAINER="bizmark_app"
DOCKER_COMPOSE_DIR="/root/osint"

case "$1" in
    status)
        echo "🔍 Checking bizmark.id status..."
        echo ""
        echo "📊 Nginx Status:"
        docker ps | grep $NGINX_CONTAINER
        echo ""
        echo "📊 App Status:"
        docker ps | grep $APP_CONTAINER
        echo ""
        echo "🌐 Testing Domain:"
        curl -I -H "Host: bizmark.id" http://localhost/ 2>&1 | head -10
        ;;
        
    logs)
        echo "📋 Nginx Logs (bizmark.id):"
        docker exec $NGINX_CONTAINER tail -f /var/log/nginx/bizmark.id-access.log
        ;;
        
    errors)
        echo "❌ Nginx Error Logs (bizmark.id):"
        docker exec $NGINX_CONTAINER tail -f /var/log/nginx/bizmark.id-error.log
        ;;
        
    reload)
        echo "🔄 Reloading Nginx..."
        docker exec $NGINX_CONTAINER nginx -t && \
        docker exec $NGINX_CONTAINER nginx -s reload && \
        echo "✅ Nginx reloaded successfully!"
        ;;
        
    restart)
        echo "🔄 Restarting Nginx container..."
        cd $DOCKER_COMPOSE_DIR
        docker-compose restart nginx
        echo "✅ Nginx restarted!"
        ;;
        
    test)
        echo "🧪 Testing nginx configuration..."
        docker exec $NGINX_CONTAINER nginx -t
        ;;
        
    cache-clear)
        echo "🧹 Clearing Laravel cache..."
        docker exec $APP_CONTAINER php artisan config:cache
        docker exec $APP_CONTAINER php artisan view:clear
        docker exec $APP_CONTAINER php artisan route:clear
        echo "✅ Cache cleared!"
        ;;
        
    ssl-setup)
        echo "🔒 Setting up SSL for bizmark.id..."
        echo "⚠️  Make sure DNS is propagated first!"
        read -p "Continue? (y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]
        then
            cd $DOCKER_COMPOSE_DIR
            docker-compose run --rm certbot certonly \
              --webroot \
              --webroot-path=/var/www/certbot \
              -d bizmark.id \
              -d www.bizmark.id \
              --email admin@bizmark.id \
              --agree-tos \
              --no-eff-email
        fi
        ;;
        
    network-info)
        echo "🌐 Network Information:"
        echo ""
        echo "📊 Nginx Networks:"
        docker inspect $NGINX_CONTAINER | grep -A 10 "Networks"
        echo ""
        echo "📊 App Networks:"
        docker inspect $APP_CONTAINER | grep -A 10 "Networks"
        ;;
        
    health)
        echo "💚 Health Check:"
        echo ""
        echo "🔍 Nginx:"
        docker exec $NGINX_CONTAINER nginx -v 2>&1
        echo ""
        echo "🔍 PHP-FPM:"
        docker exec $APP_CONTAINER php -v | head -1
        echo ""
        echo "🔍 Laravel:"
        docker exec $APP_CONTAINER php artisan --version
        echo ""
        echo "🔍 Domain Response:"
        curl -s -o /dev/null -w "HTTP Status: %{http_code}\nTime: %{time_total}s\n" -H "Host: bizmark.id" http://localhost/
        ;;
        
    *)
        echo "🚀 Bizmark.id Domain Manager"
        echo ""
        echo "Usage: $0 [command]"
        echo ""
        echo "Available commands:"
        echo "  status       - Check domain and containers status"
        echo "  logs         - View access logs"
        echo "  errors       - View error logs"
        echo "  reload       - Reload nginx configuration"
        echo "  restart      - Restart nginx container"
        echo "  test         - Test nginx configuration"
        echo "  cache-clear  - Clear Laravel cache"
        echo "  ssl-setup    - Setup SSL with Let's Encrypt"
        echo "  network-info - Show network configuration"
        echo "  health       - Run health checks"
        echo ""
        echo "Examples:"
        echo "  $0 status"
        echo "  $0 reload"
        echo "  $0 logs"
        ;;
esac
