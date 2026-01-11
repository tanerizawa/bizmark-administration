/**
 * ============================================
 * VISUAL HIERARCHY & SCROLL EFFECTS
 * Progress tracking, parallax, and flow
 * ============================================
 */

class VisualHierarchy {
  constructor() {
    this.scrollProgress = null;
    this.parallaxElements = [];
    this.disclosureElements = [];
    this.isInitialized = false;

    this.init();
  }

  /**
   * Initialize all visual hierarchy features
   */
  init() {
    if (this.isInitialized) return;

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => this.setup());
    } else {
      this.setup();
    }
  }

  /**
   * Setup all components
   */
  setup() {
    this.createScrollProgress();
    this.initParallax();
    this.initProgressiveDisclosure();
    this.initZPatternTracking();

    // Bind scroll handler with throttle
    this.bindScrollHandler();

    this.isInitialized = true;
  }

  /**
   * ============================================
   * SCROLL PROGRESS INDICATOR
   * ============================================
   */
  createScrollProgress() {
    // Check if already exists
    if (document.getElementById('scroll-progress')) return;

    // Create progress bar
    const progressContainer = document.createElement('div');
    progressContainer.id = 'scroll-progress';
    progressContainer.className = 'scroll-progress';
    progressContainer.setAttribute('role', 'progressbar');
    progressContainer.setAttribute('aria-label', 'Reading progress');

    const progressBar = document.createElement('div');
    progressBar.className = 'scroll-progress-bar';
    progressContainer.appendChild(progressBar);

    document.body.prepend(progressContainer);

    this.scrollProgress = progressBar;
  }

  /**
   * Update scroll progress
   */
  updateScrollProgress() {
    if (!this.scrollProgress) return;

    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    // Calculate progress (0 to 1)
    const progress = scrollTop / (documentHeight - windowHeight);
    const percentage = Math.min(Math.max(progress, 0), 1);

    // Update progress bar
    this.scrollProgress.style.transform = `scaleX(${percentage})`;
    this.scrollProgress.parentElement.setAttribute('aria-valuenow', Math.round(percentage * 100));
  }

  /**
   * ============================================
   * PARALLAX EFFECTS
   * ============================================
   */
  initParallax() {
    // Find all parallax elements
    this.parallaxElements = Array.from(
      document.querySelectorAll('[data-parallax-speed]')
    );

    if (this.parallaxElements.length === 0) return;

    // Respect user preferences
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      return;
    }

    // Initial positioning
    this.updateParallax();
  }

  /**
   * Update parallax positions
   */
  updateParallax() {
    const scrollTop = window.pageYOffset;

    this.parallaxElements.forEach((element) => {
      const speed = parseFloat(element.getAttribute('data-parallax-speed')) || 0.5;
      const rect = element.getBoundingClientRect();
      const elementTop = rect.top + scrollTop;
      const elementHeight = rect.height;
      const windowHeight = window.innerHeight;

      // Only apply parallax when element is in viewport
      if (
        scrollTop + windowHeight > elementTop &&
        scrollTop < elementTop + elementHeight
      ) {
        const yPos = (scrollTop - elementTop) * speed;
        element.style.transform = `translateY(${yPos}px)`;
      }
    });
  }

  /**
   * ============================================
   * PROGRESSIVE DISCLOSURE
   * ============================================
   */
  initProgressiveDisclosure() {
    // Find all disclosure triggers
    const triggers = document.querySelectorAll('.disclosure-trigger');

    triggers.forEach((trigger) => {
      const targetId = trigger.getAttribute('data-target');
      const target = document.getElementById(targetId);

      if (!target) return;

      // Store reference
      this.disclosureElements.push({ trigger, target });

      // Add click handler
      trigger.addEventListener('click', () => {
        this.toggleDisclosure(trigger, target);
      });
    });
  }

  /**
   * Toggle disclosure element
   */
  toggleDisclosure(trigger, target) {
    const isExpanded = target.classList.contains('expanded');

    if (isExpanded) {
      // Collapse
      target.classList.remove('expanded');
      trigger.classList.remove('active');
      trigger.setAttribute('aria-expanded', 'false');
    } else {
      // Expand
      target.classList.add('expanded');
      trigger.classList.add('active');
      trigger.setAttribute('aria-expanded', 'true');

      // Optional: Scroll into view
      setTimeout(() => {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'nearest',
        });
      }, 200);
    }
  }

  /**
   * ============================================
   * Z-PATTERN TRACKING
   * ============================================
   */
  initZPatternTracking() {
    // Find all Z-pattern sections
    const zSections = document.querySelectorAll('.z-pattern-section');

    if (zSections.length === 0) return;

    // Setup Intersection Observer
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('in-view');

            // Trigger diagonal animation
            const diagonalElements = entry.target.querySelectorAll('.z-diagonal');
            diagonalElements.forEach((el, index) => {
              setTimeout(() => {
                el.classList.add('in-view');
              }, index * 150);
            });
          }
        });
      },
      {
        threshold: 0.2,
        rootMargin: '0px 0px -100px 0px',
      }
    );

    zSections.forEach((section) => observer.observe(section));
  }

  /**
   * ============================================
   * SCROLL HANDLER
   * ============================================
   */
  bindScrollHandler() {
    let ticking = false;

    const handleScroll = () => {
      if (!ticking) {
        window.requestAnimationFrame(() => {
          this.updateScrollProgress();

          // Only update parallax if elements exist
          if (this.parallaxElements.length > 0) {
            this.updateParallax();
          }

          ticking = false;
        });

        ticking = true;
      }
    };

    // Bind scroll event
    window.addEventListener('scroll', handleScroll, { passive: true });

    // Initial call
    handleScroll();
  }
}

/**
 * ============================================
 * FOLD-BASED CONTENT STRATEGY
 * ============================================
 */

class FoldStrategy {
  constructor() {
    this.folds = {
      primary: null,
      secondary: null,
      tertiary: [],
    };

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
    // Identify folds
    this.folds.primary = document.querySelector('.fold-primary');
    this.folds.secondary = document.querySelector('.fold-secondary');
    this.folds.tertiary = Array.from(document.querySelectorAll('.fold-tertiary'));

    // Setup lazy loading for tertiary folds
    this.setupLazyLoading();
  }

  /**
   * Lazy load tertiary content
   */
  setupLazyLoading() {
    if (this.folds.tertiary.length === 0) return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('loaded');
            observer.unobserve(entry.target);
          }
        });
      },
      {
        rootMargin: '200px', // Load 200px before entering viewport
      }
    );

    this.folds.tertiary.forEach((fold) => {
      observer.observe(fold);
    });
  }
}

/**
 * ============================================
 * ATTENTION HOTSPOTS
 * ============================================
 */

class AttentionHotspots {
  constructor() {
    this.hotspots = [];
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
    this.hotspots = Array.from(document.querySelectorAll('.attention-hotspot'));

    if (this.hotspots.length === 0) return;

    // Track when hotspots enter viewport
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('active');

            // Optional: Send analytics event
            this.trackHotspot(entry.target);
          } else {
            entry.target.classList.remove('active');
          }
        });
      },
      {
        threshold: 0.5,
      }
    );

    this.hotspots.forEach((hotspot) => observer.observe(hotspot));
  }

  /**
   * Track hotspot view (for analytics)
   */
  trackHotspot(element) {
    const id = element.id || 'unnamed-hotspot';

    // Send to analytics if available
    if (typeof gtag === 'function') {
      gtag('event', 'hotspot_view', {
        event_category: 'engagement',
        event_label: id,
      });
    }
  }
}

/**
 * ============================================
 * AUTO-INITIALIZATION
 * ============================================
 */

// Initialize visual hierarchy system
window.visualHierarchy = new VisualHierarchy();
window.foldStrategy = new FoldStrategy();
window.attentionHotspots = new AttentionHotspots();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = {
    VisualHierarchy,
    FoldStrategy,
    AttentionHotspots,
  };
}
