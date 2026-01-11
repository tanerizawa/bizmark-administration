/**
 * ============================================
 * ANIMATED COUNTERS
 * Neuroscience-optimized number animations
 * ============================================
 *
 * Creates engaging count-up animations that trigger
 * dopamine release through progressive goal completion
 */

class AnimatedCounter {
  /**
   * Initialize counter
   * @param {HTMLElement} element - Element containing the counter
   * @param {Object} options - Configuration options
   */
  constructor(element, options = {}) {
    this.element = element;
    this.target = parseInt(element.getAttribute('data-counter')) || parseInt(element.textContent) || 0;

    // Configuration
    this.options = {
      duration: options.duration || 2000,        // Animation duration in ms
      delay: options.delay || 0,                 // Delay before start
      easing: options.easing || 'easeOutQuart',  // Easing function
      separator: options.separator || '',        // Thousand separator (e.g., ',')
      decimal: options.decimal || '',            // Decimal separator
      decimals: options.decimals || 0,           // Number of decimals
      prefix: options.prefix || '',              // Prefix (e.g., '$')
      suffix: options.suffix || '',              // Suffix (e.g., '+')
      onStart: options.onStart || null,          // Callback on start
      onUpdate: options.onUpdate || null,        // Callback on each update
      onComplete: options.onComplete || null,    // Callback on completion
      threshold: options.threshold || 0.5,       // Intersection observer threshold
    };

    this.current = 0;
    this.startTime = null;
    this.animationId = null;
    this.hasAnimated = false;

    // Setup intersection observer
    this.setupObserver();
  }

  /**
   * Setup intersection observer to trigger animation when in viewport
   */
  setupObserver() {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && !this.hasAnimated) {
            this.start();
            this.hasAnimated = true;
          }
        });
      },
      {
        threshold: this.options.threshold,
        rootMargin: '0px 0px -50px 0px'
      }
    );

    observer.observe(this.element);
  }

  /**
   * Easing functions for smooth animations
   */
  easingFunctions = {
    linear: (t) => t,
    easeOutQuart: (t) => 1 - Math.pow(1 - t, 4),
    easeOutCubic: (t) => 1 - Math.pow(1 - t, 3),
    easeOutQuad: (t) => 1 - (1 - t) * (1 - t),
    easeInOutQuart: (t) => t < 0.5 ? 8 * t * t * t * t : 1 - Math.pow(-2 * t + 2, 4) / 2,
  };

  /**
   * Format number with separators and decimals
   */
  formatNumber(num) {
    const fixed = num.toFixed(this.options.decimals);
    const parts = fixed.split('.');

    // Add thousand separator
    if (this.options.separator) {
      parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, this.options.separator);
    }

    // Join with decimal separator
    const formatted = parts.join(this.options.decimal);

    // Add prefix and suffix
    return this.options.prefix + formatted + this.options.suffix;
  }

  /**
   * Animation frame function
   */
  animate(timestamp) {
    if (!this.startTime) {
      this.startTime = timestamp;
      if (this.options.onStart) {
        this.options.onStart(this);
      }
    }

    const elapsed = timestamp - this.startTime;
    const progress = Math.min(elapsed / this.options.duration, 1);

    // Apply easing
    const easingFunc = this.easingFunctions[this.options.easing] || this.easingFunctions.easeOutQuart;
    const easedProgress = easingFunc(progress);

    // Calculate current value
    this.current = this.target * easedProgress;

    // Update element
    this.element.textContent = this.formatNumber(this.current);

    // Callback
    if (this.options.onUpdate) {
      this.options.onUpdate(this.current, progress);
    }

    // Continue or complete
    if (progress < 1) {
      this.animationId = requestAnimationFrame((t) => this.animate(t));
    } else {
      this.complete();
    }
  }

  /**
   * Start the animation
   */
  start() {
    // Clear any existing animation
    if (this.animationId) {
      cancelAnimationFrame(this.animationId);
    }

    // Add animation class
    this.element.classList.add('counter-animate');

    // Start after delay
    setTimeout(() => {
      this.current = 0;
      this.startTime = null;
      this.animationId = requestAnimationFrame((t) => this.animate(t));
    }, this.options.delay);
  }

  /**
   * Complete the animation
   */
  complete() {
    this.current = this.target;
    this.element.textContent = this.formatNumber(this.current);

    if (this.options.onComplete) {
      this.options.onComplete(this);
    }
  }

  /**
   * Reset the counter
   */
  reset() {
    if (this.animationId) {
      cancelAnimationFrame(this.animationId);
    }
    this.current = 0;
    this.startTime = null;
    this.hasAnimated = false;
    this.element.textContent = this.formatNumber(0);
    this.element.classList.remove('counter-animate');
  }
}

/**
 * ============================================
 * AUTO-INITIALIZATION
 * ============================================
 */

/**
 * Initialize all counters on page
 */
function initializeCounters() {
  // Find all elements with data-counter attribute
  const counterElements = document.querySelectorAll('[data-counter]');

  counterElements.forEach((element, index) => {
    // Get custom options from data attributes
    const options = {
      duration: parseInt(element.getAttribute('data-counter-duration')) || 2000,
      delay: parseInt(element.getAttribute('data-counter-delay')) || (index * 100), // Stagger
      separator: element.getAttribute('data-counter-separator') || '',
      prefix: element.getAttribute('data-counter-prefix') || '',
      suffix: element.getAttribute('data-counter-suffix') || '',
      decimals: parseInt(element.getAttribute('data-counter-decimals')) || 0,
      onComplete: (counter) => {
        // Add completion class for styling
        counter.element.classList.add('counter-complete');

        // Optional: trigger confetti or celebration
        if (element.hasAttribute('data-counter-celebrate')) {
          celebrateCounter(counter.element);
        }
      }
    };

    // Create counter instance
    new AnimatedCounter(element, options);
  });
}

/**
 * Celebration effect for completed counters
 */
function celebrateCounter(element) {
  // Add celebration class
  element.classList.add('counter-celebrating');

  // Remove after animation
  setTimeout(() => {
    element.classList.remove('counter-celebrating');
  }, 1000);
}

/**
 * ============================================
 * UTILITY FUNCTIONS
 * ============================================
 */

/**
 * Manually create and start a counter
 */
function createCounter(element, target, options = {}) {
  element.setAttribute('data-counter', target);
  const counter = new AnimatedCounter(element, options);
  counter.start();
  return counter;
}

/**
 * Batch update multiple counters
 */
function updateCounters(counters) {
  counters.forEach(({ element, target, options }) => {
    createCounter(element, target, options);
  });
}

// Auto-initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeCounters);
} else {
  initializeCounters();
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { AnimatedCounter, createCounter, updateCounters, initializeCounters };
}

// Make available globally
window.AnimatedCounter = AnimatedCounter;
window.createCounter = createCounter;
window.updateCounters = updateCounters;
window.initializeCounters = initializeCounters;
