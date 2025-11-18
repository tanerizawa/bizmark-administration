/**
 * PWA Update Handler
 * Automatically detects and handles service worker updates
 */

class PWAUpdateHandler {
  constructor() {
    this.registration = null;
    this.updateAvailable = false;
    this.checkInterval = 60000; // Check every 60 seconds
    this.updateCheckTimer = null;
    
    this.init();
  }
  
  async init() {
    if (!('serviceWorker' in navigator)) {
      console.log('[PWA] Service Worker not supported');
      return;
    }
    
    try {
      // Register service worker
      this.registration = await navigator.serviceWorker.register('/sw.js', {
        updateViaCache: 'none' // Always fetch latest SW
      });
      
      console.log('[PWA] Service Worker registered:', this.registration.scope);
      
      // Listen for updates
      this.setupUpdateListeners();
      
      // Check for updates periodically
      this.startUpdateCheck();
      
      // Listen for messages from service worker
      this.setupMessageListener();
      
      // Check for update on page load
      this.checkForUpdate();
      
      // Check for update when page becomes visible
      document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
          this.checkForUpdate();
        }
      });
      
    } catch (error) {
      console.error('[PWA] Service Worker registration failed:', error);
    }
  }
  
  setupUpdateListeners() {
    // Listen for new service worker installing
    this.registration.addEventListener('updatefound', () => {
      console.log('[PWA] Update found! Installing new version...');
      
      const newWorker = this.registration.installing;
      
      newWorker.addEventListener('statechange', () => {
        console.log('[PWA] New SW state:', newWorker.state);
        
        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
          // New service worker installed but old one still active
          console.log('[PWA] New version available!');
          this.updateAvailable = true;
          this.showUpdatePrompt();
        }
      });
    });
    
    // Listen for controller change (new SW took control)
    navigator.serviceWorker.addEventListener('controllerchange', () => {
      console.log('[PWA] Controller changed - reloading page');
      
      // Show update notification
      this.showUpdateNotification('Aplikasi telah diperbarui!');
      
      // Reload page to get new content
      if (!this.isReloading) {
        this.isReloading = true;
        window.location.reload();
      }
    });
  }
  
  setupMessageListener() {
    navigator.serviceWorker.addEventListener('message', (event) => {
      console.log('[PWA] Message from SW:', event.data);
      
      if (event.data.type === 'SW_UPDATED') {
        console.log('[PWA] SW updated to version:', event.data.version);
        this.showUpdateNotification(`Update berhasil! Versi ${event.data.version}`);
      }
    });
  }
  
  startUpdateCheck() {
    // Check for updates every minute
    this.updateCheckTimer = setInterval(() => {
      this.checkForUpdate();
    }, this.checkInterval);
    
    console.log('[PWA] Started periodic update check');
  }
  
  async checkForUpdate() {
    if (!this.registration) return;
    
    try {
      console.log('[PWA] Checking for updates...');
      await this.registration.update();
    } catch (error) {
      console.error('[PWA] Update check failed:', error);
    }
  }
  
  showUpdatePrompt() {
    // Create update banner
    const banner = document.createElement('div');
    banner.id = 'pwa-update-banner';
    banner.className = 'fixed top-0 left-0 right-0 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3 z-50 shadow-lg transform -translate-y-full transition-transform duration-300';
    banner.style.animation = 'slideDown 0.3s ease-out forwards';
    
    banner.innerHTML = `
      <div class="container mx-auto flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
          <i class="fas fa-sync-alt text-xl animate-spin"></i>
          <div>
            <p class="font-semibold text-sm">Update Tersedia</p>
            <p class="text-xs text-white/80">Versi baru aplikasi sudah siap diinstal</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <button 
            onclick="pwaUpdateHandler.installUpdate()" 
            class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-50 transition-colors focus:outline-none focus:ring-2 focus:ring-white/50"
          >
            <i class="fas fa-download mr-2"></i>Update Sekarang
          </button>
          <button 
            onclick="pwaUpdateHandler.dismissUpdate()" 
            class="text-white/80 hover:text-white px-3 py-2 rounded-lg text-sm transition-colors focus:outline-none"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
    `;
    
    // Add animation styles
    const style = document.createElement('style');
    style.textContent = `
      @keyframes slideDown {
        from {
          transform: translateY(-100%);
        }
        to {
          transform: translateY(0);
        }
      }
      
      @keyframes slideUp {
        from {
          transform: translateY(0);
        }
        to {
          transform: translateY(-100%);
        }
      }
      
      #pwa-update-banner.hiding {
        animation: slideUp 0.3s ease-out forwards;
      }
    `;
    
    if (!document.getElementById('pwa-update-styles')) {
      style.id = 'pwa-update-styles';
      document.head.appendChild(style);
    }
    
    document.body.appendChild(banner);
    
    // Auto-dismiss after 30 seconds if user doesn't interact
    setTimeout(() => {
      if (document.getElementById('pwa-update-banner')) {
        this.dismissUpdate();
      }
    }, 30000);
    
    // Vibrate if supported
    if ('vibrate' in navigator) {
      navigator.vibrate([200, 100, 200]);
    }
  }
  
  installUpdate() {
    console.log('[PWA] Installing update...');
    
    // Show loading state
    const banner = document.getElementById('pwa-update-banner');
    if (banner) {
      banner.innerHTML = `
        <div class="container mx-auto flex items-center justify-center gap-3">
          <i class="fas fa-spinner fa-spin text-xl"></i>
          <p class="text-sm font-semibold">Menginstal update...</p>
        </div>
      `;
    }
    
    // Tell the waiting service worker to activate
    if (this.registration.waiting) {
      this.registration.waiting.postMessage({ type: 'SKIP_WAITING' });
    } else {
      // No waiting worker, just reload
      window.location.reload();
    }
  }
  
  dismissUpdate() {
    const banner = document.getElementById('pwa-update-banner');
    if (banner) {
      banner.classList.add('hiding');
      setTimeout(() => {
        banner.remove();
      }, 300);
    }
  }
  
  showUpdateNotification(message) {
    // Show toast notification
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-3 transform translate-x-full transition-transform duration-300';
    toast.innerHTML = `
      <i class="fas fa-check-circle text-xl"></i>
      <p class="font-medium">${message}</p>
    `;
    
    document.body.appendChild(toast);
    
    // Slide in
    setTimeout(() => {
      toast.style.transform = 'translateX(0)';
    }, 10);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
      toast.style.transform = 'translateX(150%)';
      setTimeout(() => {
        toast.remove();
      }, 300);
    }, 3000);
  }
  
  // Manual check for updates (can be called from UI)
  async manualUpdateCheck() {
    console.log('[PWA] Manual update check initiated');
    
    this.showUpdateNotification('Memeriksa pembaruan...');
    
    await this.checkForUpdate();
    
    setTimeout(() => {
      if (!this.updateAvailable) {
        this.showUpdateNotification('Aplikasi sudah versi terbaru');
      }
    }, 2000);
  }
  
  // Get current version info
  async getVersionInfo() {
    return new Promise((resolve) => {
      const messageChannel = new MessageChannel();
      
      messageChannel.port1.onmessage = (event) => {
        resolve(event.data);
      };
      
      if (navigator.serviceWorker.controller) {
        navigator.serviceWorker.controller.postMessage(
          { type: 'CHECK_VERSION' },
          [messageChannel.port2]
        );
      } else {
        resolve({ version: 'Unknown', timestamp: null });
      }
    });
  }
}

// Initialize PWA Update Handler
let pwaUpdateHandler;

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    pwaUpdateHandler = new PWAUpdateHandler();
  });
} else {
  pwaUpdateHandler = new PWAUpdateHandler();
}

// Expose to window for manual controls
window.pwaUpdateHandler = pwaUpdateHandler;

console.log('[PWA] Update handler loaded');

