#!/bin/bash
# ========================================
# Bizmark.id Development Server Helper
# Resource-efficient: Start only when needed
# ========================================

PID_FILE="/tmp/bizmark-server.pid"
LOG_FILE="/root/Bizmark.id/storage/logs/dev-server.log"

case "$1" in
  start)
    if [ -f "$PID_FILE" ]; then
      PID=$(cat "$PID_FILE")
      if ps -p "$PID" > /dev/null 2>&1; then
        echo "✅ Server sudah running di PID $PID"
        echo "🌐 URL: http://127.0.0.1:8000"
        exit 0
      fi
    fi
    
    echo "🚀 Starting Bizmark.id server..."
    cd /root/Bizmark.id
    nohup php artisan serve > "$LOG_FILE" 2>&1 &
    echo $! > "$PID_FILE"
    sleep 2
    
    if ps -p $(cat "$PID_FILE") > /dev/null 2>&1; then
      echo "✅ Server started successfully!"
      echo "🌐 URL: http://127.0.0.1:8000"
      echo "📝 Logs: tail -f $LOG_FILE"
    else
      echo "❌ Failed to start server. Check logs: $LOG_FILE"
      rm -f "$PID_FILE"
      exit 1
    fi
    ;;
    
  stop)
    if [ ! -f "$PID_FILE" ]; then
      echo "⚠️  Server tidak running"
      exit 0
    fi
    
    PID=$(cat "$PID_FILE")
    if ps -p "$PID" > /dev/null 2>&1; then
      echo "🛑 Stopping server (PID: $PID)..."
      kill "$PID"
      rm -f "$PID_FILE"
      echo "✅ Server stopped (resource freed)"
    else
      echo "⚠️  Process not found, cleaning up..."
      rm -f "$PID_FILE"
    fi
    ;;
    
  restart)
    $0 stop
    sleep 1
    $0 start
    ;;
    
  status)
    if [ -f "$PID_FILE" ]; then
      PID=$(cat "$PID_FILE")
      if ps -p "$PID" > /dev/null 2>&1; then
        echo "✅ Server RUNNING"
        echo "   PID: $PID"
        echo "   URL: http://127.0.0.1:8000"
        echo "   Memory: $(ps -o rss= -p $PID | awk '{print $1/1024 " MB"}')"
      else
        echo "❌ Server STOPPED (stale PID file)"
        rm -f "$PID_FILE"
      fi
    else
      echo "⚠️  Server NOT RUNNING"
    fi
    ;;
    
  logs)
    if [ -f "$LOG_FILE" ]; then
      tail -f "$LOG_FILE"
    else
      echo "⚠️  No logs yet. Start server first."
    fi
    ;;
    
  *)
    echo "Bizmark.id Development Server Helper"
    echo ""
    echo "Usage: $0 {start|stop|restart|status|logs}"
    echo ""
    echo "Commands:"
    echo "  start    - Start Laravel server (port 8000)"
    echo "  stop     - Stop server & free resources"
    echo "  restart  - Restart server"
    echo "  status   - Check server status & memory usage"
    echo "  logs     - View server logs (live)"
    echo ""
    echo "Resource-efficient: Server hanya jalan saat development"
    exit 1
    ;;
esac
