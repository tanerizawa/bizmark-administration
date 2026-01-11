/**
 * ============================================
 * MICRO-INTERACTIONS & DELIGHT
 * Form feedback, celebrations, haptic
 * ============================================
 */

class MicroInteractions {
  constructor() {
    this.init();
  }

  init() {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => this.setup());
    } else {
      this.setup();
    }
  }

  setup() {
    this.initFormFeedback();
    this.initHapticFeedback();
    this.initTooltips();
    this.initRippleEffect();
  }

  /**
   * ============================================
   * FORM FEEDBACK & VALIDATION
   * ============================================
   */
  initFormFeedback() {
    // Find all form inputs
    const inputs = document.querySelectorAll(
      'input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]), textarea'
    );

    inputs.forEach((input) => {
      // Wrap in validation container if not already
      if (!input.parentElement.classList.contains('input-wrapper')) {
        const wrapper = document.createElement('div');
        wrapper.className = 'input-wrapper';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        // Add validation icon
        const icon = document.createElement('i');
        icon.className = 'input-validation-icon fas fa-check-circle';
        wrapper.appendChild(icon);
      }

      // Real-time validation on input
      input.addEventListener('input', () => {
        this.validateInput(input);
      });

      // Validation on blur
      input.addEventListener('blur', () => {
        this.validateInput(input, true);
      });
    });
  }

  /**
   * Validate individual input
   */
  validateInput(input, showError = false) {
    const wrapper = input.closest('.input-wrapper');
    if (!wrapper) return;

    const value = input.value.trim();
    const type = input.type;
    const required = input.hasAttribute('required');
    let isValid = true;
    let errorMessage = '';

    // Required check
    if (required && !value) {
      isValid = false;
      errorMessage = 'This field is required';
    }

    // Email validation
    if (type === 'email' && value) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        isValid = false;
        errorMessage = 'Please enter a valid email';
      }
    }

    // Phone validation
    if (input.name?.includes('phone') && value) {
      const phoneRegex = /^[\d\s\-\+\(\)]+$/;
      if (!phoneRegex.test(value) || value.replace(/\D/g, '').length < 10) {
        isValid = false;
        errorMessage = 'Please enter a valid phone number';
      }
    }

    // URL validation
    if (type === 'url' && value) {
      try {
        new URL(value);
      } catch {
        isValid = false;
        errorMessage = 'Please enter a valid URL';
      }
    }

    // Min length
    const minLength = input.getAttribute('minlength');
    if (minLength && value.length > 0 && value.length < parseInt(minLength)) {
      isValid = false;
      errorMessage = `Minimum ${minLength} characters required`;
    }

    // Update UI
    wrapper.classList.remove('success', 'error');
    input.classList.remove('success', 'error');

    if (value) {
      if (isValid) {
        wrapper.classList.add('success');
        input.classList.add('success');
        const icon = wrapper.querySelector('.input-validation-icon');
        if (icon) {
          icon.className = 'input-validation-icon fas fa-check-circle';
        }
      } else if (showError) {
        wrapper.classList.add('error');
        input.classList.add('error');
        const icon = wrapper.querySelector('.input-validation-icon');
        if (icon) {
          icon.className = 'input-validation-icon fas fa-exclamation-circle';
        }

        // Show error message
        this.showFieldError(input, errorMessage);
      }
    }

    return isValid;
  }

  /**
   * Show error message for field
   */
  showFieldError(input, message) {
    // Remove existing error
    const existingError = input.parentElement.querySelector('.field-error');
    if (existingError) {
      existingError.remove();
    }

    // Add new error
    const error = document.createElement('div');
    error.className = 'field-error text-sm text-red-600 mt-1';
    error.textContent = message;
    input.parentElement.appendChild(error);

    // Remove after 3 seconds
    setTimeout(() => {
      error.style.opacity = '0';
      setTimeout(() => error.remove(), 300);
    }, 3000);
  }

  /**
   * ============================================
   * HAPTIC FEEDBACK (Vibration)
   * ============================================
   */
  initHapticFeedback() {
    // Check if vibration API is supported
    if (!('vibrate' in navigator)) return;

    // Add haptic to buttons
    const buttons = document.querySelectorAll('button, .btn, a[role="button"]');

    buttons.forEach((button) => {
      button.addEventListener('click', (e) => {
        // Light vibration on click
        navigator.vibrate(10);
      });
    });

    // Add haptic to form submissions
    const forms = document.querySelectorAll('form');
    forms.forEach((form) => {
      form.addEventListener('submit', (e) => {
        // Success vibration pattern
        navigator.vibrate([50, 100, 50]);
      });
    });
  }

  /**
   * Trigger custom haptic feedback
   */
  static haptic(pattern = 10) {
    if ('vibrate' in navigator) {
      navigator.vibrate(pattern);
    }
  }

  /**
   * ============================================
   * TOOLTIPS
   * ============================================
   */
  initTooltips() {
    // Find all elements with data-tooltip
    const elements = document.querySelectorAll('[data-tooltip]');

    elements.forEach((element) => {
      const text = element.getAttribute('data-tooltip');
      const position = element.getAttribute('data-tooltip-position') || 'top';

      // Create tooltip
      const tooltip = document.createElement('div');
      tooltip.className = 'tooltip-content';
      tooltip.textContent = text;

      // Wrap element if not already wrapped
      if (!element.parentElement.classList.contains('tooltip-wrapper')) {
        const wrapper = document.createElement('div');
        wrapper.className = 'tooltip-wrapper';
        element.parentNode.insertBefore(wrapper, element);
        wrapper.appendChild(element);
        wrapper.appendChild(tooltip);
      }
    });
  }

  /**
   * ============================================
   * RIPPLE EFFECT
   * ============================================
   */
  initRippleEffect() {
    const elements = document.querySelectorAll('.btn, button, [data-ripple]');

    elements.forEach((element) => {
      element.addEventListener('click', function (e) {
        const ripple = document.createElement('span');
        ripple.className = 'ripple-effect';
        this.appendChild(ripple);

        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.style.cssText = `
          position: absolute;
          width: ${size}px;
          height: ${size}px;
          border-radius: 50%;
          background: rgba(255, 255, 255, 0.6);
          left: ${x}px;
          top: ${y}px;
          pointer-events: none;
          animation: ripple 600ms ease-out;
        `;

        setTimeout(() => ripple.remove(), 600);
      });
    });

    // Add ripple animation to stylesheet
    if (!document.getElementById('ripple-animation')) {
      const style = document.createElement('style');
      style.id = 'ripple-animation';
      style.textContent = `
        @keyframes ripple {
          to {
            transform: scale(4);
            opacity: 0;
          }
        }
      `;
      document.head.appendChild(style);
    }
  }
}

/**
 * ============================================
 * CONFETTI CELEBRATION
 * ============================================
 */

class Confetti {
  constructor(options = {}) {
    this.options = {
      particleCount: options.particleCount || 100,
      spread: options.spread || 70,
      origin: options.origin || { y: 0.6 },
      colors: options.colors || ['#5B8DBE', '#E8956F', '#7CB342', '#FFD700'],
      duration: options.duration || 3000,
    };
  }

  /**
   * Fire confetti
   */
  fire() {
    const container = document.createElement('div');
    container.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 9999;
    `;
    document.body.appendChild(container);

    // Create particles
    for (let i = 0; i < this.options.particleCount; i++) {
      this.createParticle(container, i);
    }

    // Remove container after animation
    setTimeout(() => {
      container.remove();
    }, this.options.duration);
  }

  /**
   * Create single confetti particle
   */
  createParticle(container, index) {
    const particle = document.createElement('div');
    const color = this.options.colors[Math.floor(Math.random() * this.options.colors.length)];
    const size = Math.random() * 10 + 5;
    const startX = window.innerWidth * (this.options.origin.x || 0.5);
    const startY = window.innerHeight * this.options.origin.y;

    // Random direction
    const angle = (Math.random() * this.options.spread - this.options.spread / 2) * (Math.PI / 180);
    const velocity = Math.random() * 500 + 200;
    const endX = startX + Math.sin(angle) * velocity;
    const endY = startY - Math.cos(angle) * velocity;

    particle.style.cssText = `
      position: absolute;
      width: ${size}px;
      height: ${size}px;
      background: ${color};
      left: ${startX}px;
      top: ${startY}px;
      pointer-events: none;
      border-radius: ${Math.random() > 0.5 ? '50%' : '0'};
    `;

    container.appendChild(particle);

    // Animate
    const animation = particle.animate(
      [
        {
          transform: 'translate(0, 0) rotate(0deg)',
          opacity: 1,
        },
        {
          transform: `translate(${endX - startX}px, ${endY - startY + window.innerHeight}px) rotate(${Math.random() * 720}deg)`,
          opacity: 0,
        },
      ],
      {
        duration: this.options.duration,
        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
      }
    );

    animation.onfinish = () => particle.remove();
  }

  /**
   * Static method to quickly fire confetti
   */
  static celebrate(options = {}) {
    const confetti = new Confetti(options);
    confetti.fire();

    // Haptic feedback
    MicroInteractions.haptic([50, 100, 50]);
  }
}

/**
 * ============================================
 * TOAST NOTIFICATIONS
 * ============================================
 */

class Toast {
  static show(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    const icons = {
      success: 'fa-check-circle',
      error: 'fa-exclamation-circle',
      warning: 'fa-exclamation-triangle',
      info: 'fa-info-circle',
    };

    const colors = {
      success: '#7CB342',
      error: '#C97C7C',
      warning: '#D4A574',
      info: '#5B8DBE',
    };

    toast.className = 'toast-notification';
    toast.style.cssText = `
      position: fixed;
      bottom: 2rem;
      right: 2rem;
      min-width: 300px;
      max-width: 400px;
      padding: 1rem 1.5rem;
      background: white;
      border-left: 4px solid ${colors[type]};
      border-radius: 8px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
      display: flex;
      align-items: center;
      gap: 1rem;
      z-index: 10000;
      animation: toast-slide-in 0.3s ease-out;
    `;

    toast.innerHTML = `
      <i class="fas ${icons[type]} text-2xl" style="color: ${colors[type]}"></i>
      <div class="flex-1">
        <div class="font-semibold text-gray-900">${message}</div>
      </div>
      <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 ml-2">
        <i class="fas fa-times"></i>
      </button>
    `;

    // Add animation
    if (!document.getElementById('toast-animation')) {
      const style = document.createElement('style');
      style.id = 'toast-animation';
      style.textContent = `
        @keyframes toast-slide-in {
          from {
            transform: translateX(400px);
            opacity: 0;
          }
          to {
            transform: translateX(0);
            opacity: 1;
          }
        }
        @keyframes toast-slide-out {
          from {
            transform: translateX(0);
            opacity: 1;
          }
          to {
            transform: translateX(400px);
            opacity: 0;
          }
        }
      `;
      document.head.appendChild(style);
    }

    document.body.appendChild(toast);

    // Auto remove
    setTimeout(() => {
      toast.style.animation = 'toast-slide-out 0.3s ease-out';
      setTimeout(() => toast.remove(), 300);
    }, duration);

    // Haptic feedback
    MicroInteractions.haptic(10);
  }
}

/**
 * ============================================
 * SUCCESS CELEBRATION
 * ============================================
 */

function celebrateSuccess(message = 'Success!') {
  // Show toast
  Toast.show(message, 'success');

  // Fire confetti
  Confetti.celebrate({
    particleCount: 50,
    spread: 60,
  });

  // Haptic feedback
  MicroInteractions.haptic([50, 100, 50]);
}

/**
 * ============================================
 * AUTO-INITIALIZATION
 * ============================================
 */

// Initialize micro-interactions
window.microInteractions = new MicroInteractions();

// Make utilities globally available
window.Confetti = Confetti;
window.Toast = Toast;
window.celebrateSuccess = celebrateSuccess;

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = {
    MicroInteractions,
    Confetti,
    Toast,
    celebrateSuccess,
  };
}
