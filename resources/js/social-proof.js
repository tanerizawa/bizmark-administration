/**
 * ============================================
 * SOCIAL PROOF NOTIFICATION SYSTEM
 * Live activity notifications (FOMO trigger)
 * ============================================
 *
 * Creates floating notifications showing recent activity
 * to trigger Fear of Missing Out and increase trust
 */

class SocialProofNotification {
  constructor(options = {}) {
    this.options = {
      // Display settings
      position: options.position || 'bottom-left', // 'bottom-left', 'bottom-right', 'top-left', 'top-right'
      displayDuration: options.displayDuration || 5000, // How long to show each notification
      delayBetween: options.delayBetween || 8000, // Delay between notifications
      maxVisible: options.maxVisible || 1, // Max simultaneous notifications
      startDelay: options.startDelay || 3000, // Delay before first notification

      // Animation settings
      animationIn: options.animationIn || 'slide-in',
      animationOut: options.animationOut || 'slide-out',

      // Data settings
      loop: options.loop !== false, // Loop through notifications
      shuffle: options.shuffle !== false, // Randomize order

      // Callbacks
      onShow: options.onShow || null,
      onHide: options.onHide || null,
      onClick: options.onClick || null,
    };

    // Sample notifications (can be replaced with real data)
    this.notifications = options.notifications || this.getDefaultNotifications();

    this.currentIndex = 0;
    this.activeNotifications = [];
    this.container = null;
    this.intervalId = null;
    this.isRunning = false;

    // Shuffle if enabled
    if (this.options.shuffle) {
      this.shuffleArray(this.notifications);
    }

    this.init();
  }

  /**
   * Default notification data
   */
  getDefaultNotifications() {
    return [
      {
        type: 'permit_approved',
        icon: 'fas fa-check-circle',
        iconColor: 'text-green-500',
        company: 'PT Mandiri Jaya',
        action: 'Izin AMDAL disetujui',
        time: '2 menit yang lalu',
        avatar: null,
      },
      {
        type: 'consultation',
        icon: 'fas fa-comments',
        iconColor: 'text-blue-500',
        company: 'PT Sukses Makmur',
        action: 'Memulai konsultasi izin UKL-UPL',
        time: '15 menit yang lalu',
        avatar: null,
      },
      {
        type: 'document_submitted',
        icon: 'fas fa-file-check',
        iconColor: 'text-primary',
        company: 'CV Berkah Sentosa',
        action: 'Dokumen LB3 tersubmit',
        time: '1 jam yang lalu',
        avatar: null,
      },
      {
        type: 'project_completed',
        icon: 'fas fa-trophy',
        iconColor: 'text-yellow-500',
        company: 'PT Industri Gemilang',
        action: 'Proyek perizinan selesai 100%',
        time: '2 jam yang lalu',
        avatar: null,
      },
      {
        type: 'new_client',
        icon: 'fas fa-user-plus',
        iconColor: 'text-purple-500',
        company: 'PT Global Resources',
        action: 'Bergabung sebagai klien baru',
        time: '3 jam yang lalu',
        avatar: null,
      },
      {
        type: 'rating',
        icon: 'fas fa-star',
        iconColor: 'text-yellow-400',
        company: 'PT Maju Bersama',
        action: 'Memberikan rating 5.0',
        time: '5 jam yang lalu',
        avatar: null,
      },
    ];
  }

  /**
   * Initialize the notification system
   */
  init() {
    this.createContainer();
    this.injectStyles();

    // Start showing notifications after initial delay
    setTimeout(() => {
      this.start();
    }, this.options.startDelay);
  }

  /**
   * Create notification container
   */
  createContainer() {
    this.container = document.createElement('div');
    this.container.id = 'social-proof-container';
    this.container.className = `fixed z-50 ${this.getPositionClasses()}`;
    document.body.appendChild(this.container);
  }

  /**
   * Get position classes based on settings
   */
  getPositionClasses() {
    const positions = {
      'bottom-left': 'bottom-6 left-6',
      'bottom-right': 'bottom-6 right-6',
      'top-left': 'top-24 left-6',
      'top-right': 'top-24 right-6',
    };
    return positions[this.options.position] || positions['bottom-left'];
  }

  /**
   * Inject CSS styles
   */
  injectStyles() {
    if (document.getElementById('social-proof-styles')) return;

    const styles = document.createElement('style');
    styles.id = 'social-proof-styles';
    styles.textContent = `
      .social-proof-notification {
        max-width: 360px;
        margin-bottom: 1rem;
        pointer-events: auto;
      }

      @keyframes slideInLeft {
        from {
          transform: translateX(-100%);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }

      @keyframes slideOutLeft {
        from {
          transform: translateX(0);
          opacity: 1;
        }
        to {
          transform: translateX(-100%);
          opacity: 0;
        }
      }

      @keyframes slideInRight {
        from {
          transform: translateX(100%);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }

      @keyframes slideOutRight {
        from {
          transform: translateX(0);
          opacity: 1;
        }
        to {
          transform: translateX(100%);
          opacity: 0;
        }
      }

      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: translateY(20px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      @keyframes fadeOut {
        from {
          opacity: 1;
          transform: translateY(0);
        }
        to {
          opacity: 0;
          transform: translateY(20px);
        }
      }

      .social-proof-slide-in-left {
        animation: slideInLeft 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      }

      .social-proof-slide-out-left {
        animation: slideOutLeft 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      }

      .social-proof-slide-in-right {
        animation: slideInRight 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      }

      .social-proof-slide-out-right {
        animation: slideOutRight 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      }

      .social-proof-fade-in {
        animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      }

      .social-proof-fade-out {
        animation: fadeOut 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      }

      @media (max-width: 640px) {
        .social-proof-notification {
          max-width: calc(100vw - 3rem);
        }
      }
    `;
    document.head.appendChild(styles);
  }

  /**
   * Create notification element
   */
  createNotification(data) {
    const notification = document.createElement('div');
    notification.className = 'social-proof-notification';

    const animationClass = this.getAnimationInClass();

    notification.innerHTML = `
      <div class="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-gray-200/50 p-4 cursor-pointer hover:shadow-3xl transition-all duration-300 ${animationClass}">
        <div class="flex items-start gap-3">
          ${
            data.avatar
              ? `<img src="${data.avatar}" alt="${data.company}" class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-md" />`
              : `<div class="h-12 w-12 rounded-full bg-gradient-to-br from-primary/10 to-secondary/10 flex items-center justify-center flex-shrink-0">
                   <i class="${data.icon} text-xl ${data.iconColor}"></i>
                 </div>`
          }

          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-900 truncate">${data.company}</p>
            <p class="text-sm text-gray-600 leading-snug mt-0.5">${data.action}</p>
            <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
              <i class="far fa-clock"></i>
              <span>${data.time}</span>
            </p>
          </div>

          <button class="text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0" onclick="this.closest('.social-proof-notification').remove()">
            <i class="fas fa-times text-sm"></i>
          </button>
        </div>
      </div>
    `;

    // Add click handler
    if (this.options.onClick) {
      notification.addEventListener('click', (e) => {
        if (!e.target.closest('button')) {
          this.options.onClick(data);
        }
      });
    }

    return notification;
  }

  /**
   * Get animation in class
   */
  getAnimationInClass() {
    const isLeft = this.options.position.includes('left');
    return isLeft ? 'social-proof-slide-in-left' : 'social-proof-slide-in-right';
  }

  /**
   * Get animation out class
   */
  getAnimationOutClass() {
    const isLeft = this.options.position.includes('left');
    return isLeft ? 'social-proof-slide-out-left' : 'social-proof-slide-out-right';
  }

  /**
   * Show next notification
   */
  showNext() {
    // Check max visible limit
    if (this.activeNotifications.length >= this.options.maxVisible) {
      return;
    }

    // Get next notification data
    const data = this.notifications[this.currentIndex];
    if (!data) return;

    // Create and show notification
    const notification = this.createNotification(data);
    this.container.appendChild(notification);
    this.activeNotifications.push(notification);

    // Callback
    if (this.options.onShow) {
      this.options.onShow(data);
    }

    // Schedule removal
    setTimeout(() => {
      this.hideNotification(notification);
    }, this.options.displayDuration);

    // Update index
    this.currentIndex++;
    if (this.currentIndex >= this.notifications.length) {
      if (this.options.loop) {
        this.currentIndex = 0;
        if (this.options.shuffle) {
          this.shuffleArray(this.notifications);
        }
      } else {
        this.stop();
      }
    }
  }

  /**
   * Hide notification
   */
  hideNotification(notification) {
    const animationClass = this.getAnimationOutClass();
    notification.firstElementChild.classList.add(animationClass);

    setTimeout(() => {
      notification.remove();
      this.activeNotifications = this.activeNotifications.filter((n) => n !== notification);

      if (this.options.onHide) {
        this.options.onHide();
      }
    }, 400);
  }

  /**
   * Start showing notifications
   */
  start() {
    if (this.isRunning) return;

    this.isRunning = true;
    this.showNext();

    this.intervalId = setInterval(() => {
      this.showNext();
    }, this.options.delayBetween);
  }

  /**
   * Stop showing notifications
   */
  stop() {
    this.isRunning = false;
    if (this.intervalId) {
      clearInterval(this.intervalId);
      this.intervalId = null;
    }
  }

  /**
   * Destroy the notification system
   */
  destroy() {
    this.stop();
    if (this.container) {
      this.container.remove();
    }
  }

  /**
   * Utility: Shuffle array
   */
  shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
  }
}

/**
 * ============================================
 * AUTO-INITIALIZATION
 * ============================================
 */

// Initialize on page load (only on landing page)
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initSocialProof);
} else {
  initSocialProof();
}

function initSocialProof() {
  // Only initialize on landing pages
  const isLandingPage =
    window.location.pathname === '/' ||
    window.location.pathname === '/en' ||
    window.location.pathname.includes('/landing');

  if (!isLandingPage) return;

  // Initialize with default settings
  window.socialProofNotification = new SocialProofNotification({
    position: 'bottom-left',
    displayDuration: 6000,
    delayBetween: 10000,
    startDelay: 5000,
    maxVisible: 1,
  });
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = SocialProofNotification;
}

// Make available globally
window.SocialProofNotification = SocialProofNotification;
