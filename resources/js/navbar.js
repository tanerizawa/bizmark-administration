/**
 * Neuroscience-Based Navigation System
 * Handles scroll behavior, active section detection, and mobile menu
 */

// ============================================
// NAVBAR SCROLL BEHAVIOR
// ============================================

const navbar = document.getElementById('navbar');
let lastScrollTop = 0;
let scrollTimeout;

/**
 * Handle navbar scroll behavior
 * Changes navbar appearance based on scroll position
 */
function handleNavbarScroll() {
  const scrollTop = window.scrollY || document.documentElement.scrollTop;
  
  if (scrollTimeout) {
    clearTimeout(scrollTimeout);
  }
  
  scrollTimeout = setTimeout(() => {
    if (scrollTop > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  }, 10);
  
  lastScrollTop = scrollTop;
}

// Add scroll event listener
window.addEventListener('scroll', handleNavbarScroll, { passive: true });

// ============================================
// ACTIVE SECTION DETECTION
// ============================================

/**
 * Detect which section is currently in view
 * Updates active nav link accordingly
 */
function updateActiveSection() {
  const sections = document.querySelectorAll('[id]');
  const navLinks = document.querySelectorAll('.nav-link');
  
  let currentSection = '';
  
  sections.forEach(section => {
    const sectionTop = section.offsetTop;
    const sectionHeight = section.clientHeight;
    
    if (window.scrollY >= sectionTop - 100) {
      currentSection = section.getAttribute('id');
    }
  });
  
  // Update active nav links
  navLinks.forEach(link => {
    const section = link.getAttribute('data-section');
    
    if (section === currentSection) {
      link.classList.add('active');
      link.setAttribute('aria-current', 'page');
    } else {
      link.classList.remove('active');
      link.removeAttribute('aria-current');
    }
  });
}

// Update active section on scroll
window.addEventListener('scroll', updateActiveSection, { passive: true });

// ============================================
// MOBILE MENU TOGGLE
// ============================================

/**
 * Toggle mobile menu visibility
 */
function toggleMobileMenu() {
  const mobileMenu = document.getElementById('mobileMenu');
  const mobileMenuButton = document.getElementById('mobile-menu-button');
  const body = document.body;
  
  if (!mobileMenu) return;
  
  const isHidden = mobileMenu.classList.contains('hidden');
  
  if (isHidden) {
    // Open menu
    mobileMenu.classList.remove('hidden');
    mobileMenuButton.setAttribute('aria-expanded', 'true');
    body.style.overflow = 'hidden';
    
    // Add haptic feedback if available
    if (navigator.vibrate) {
      navigator.vibrate(10);
    }
  } else {
    // Close menu
    mobileMenu.classList.add('hidden');
    mobileMenuButton.setAttribute('aria-expanded', 'false');
    body.style.overflow = '';
    
    // Add haptic feedback if available
    if (navigator.vibrate) {
      navigator.vibrate(10);
    }
  }
}

// Close mobile menu when clicking on a link
document.addEventListener('click', (e) => {
  const mobileMenu = document.getElementById('mobileMenu');
  const mobileMenuButton = document.getElementById('mobile-menu-button');
  
  if (!mobileMenu || !mobileMenuButton) return;
  
  // Check if click is on a nav link inside mobile menu
  if (e.target.closest('.mobile-menu a') && !mobileMenu.classList.contains('hidden')) {
    // Don't close if it's the menu button
    if (!e.target.closest('#mobile-menu-button')) {
      toggleMobileMenu();
    }
  }
});

// Close mobile menu on escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
      toggleMobileMenu();
    }
  }
});

// ============================================
// SMOOTH SCROLL BEHAVIOR
// ============================================

/**
 * Handle smooth scrolling for anchor links
 */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const href = this.getAttribute('href');
    
    // Skip if href is just '#'
    if (href === '#') return;
    
    const target = document.querySelector(href);
    
    if (target) {
      e.preventDefault();
      
      // Close mobile menu if open
      const mobileMenu = document.getElementById('mobileMenu');
      if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
        toggleMobileMenu();
      }
      
      // Scroll to target with offset for navbar
      const navbarHeight = navbar.offsetHeight;
      const targetPosition = target.offsetTop - navbarHeight - 20;
      
      window.scrollTo({
        top: targetPosition,
        behavior: 'smooth'
      });
      
      // Update URL without page reload
      window.history.pushState(null, null, href);
    }
  });
});

// ============================================
// BACK TO TOP BUTTON
// ============================================

/**
 * Show/hide back to top button based on scroll position
 */
function handleBackToTopButton() {
  const backToTopBtn = document.getElementById('backToTop');
  
  if (!backToTopBtn) return;
  
  if (window.scrollY > 300) {
    backToTopBtn.classList.add('show');
  } else {
    backToTopBtn.classList.remove('show');
  }
}

window.addEventListener('scroll', handleBackToTopButton, { passive: true });

// ============================================
// KEYBOARD NAVIGATION
// ============================================

/**
 * Handle keyboard navigation for accessibility
 */
document.addEventListener('keydown', (e) => {
  // Skip if user is typing in an input
  if (e.target.matches('input, textarea, select')) return;
  
  // Alt + H: Go to home
  if (e.altKey && e.key === 'h') {
    e.preventDefault();
    const homeLink = document.querySelector('[data-section="home"]');
    if (homeLink) homeLink.click();
  }
  
  // Alt + S: Go to services
  if (e.altKey && e.key === 's') {
    e.preventDefault();
    const servicesLink = document.querySelector('[data-section="services"]');
    if (servicesLink) servicesLink.click();
  }
  
  // Alt + C: Go to contact
  if (e.altKey && e.key === 'c') {
    e.preventDefault();
    const contactLink = document.querySelector('a[href*="#contact"]');
    if (contactLink) contactLink.click();
  }
});

// ============================================
// INITIALIZATION
// ============================================

/**
 * Initialize navbar on page load
 */
document.addEventListener('DOMContentLoaded', () => {
  // Update active section on load
  updateActiveSection();
  
  // Check initial scroll position
  handleNavbarScroll();
  
  // Check back to top button
  handleBackToTopButton();
  
  // Log initialization
  console.log('✅ Neuroscience Navigation System initialized');
});

// ============================================
// PERFORMANCE OPTIMIZATION
// ============================================

/**
 * Debounce function for scroll events
 */
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Throttle function for scroll events
 */
function throttle(func, limit) {
  let inThrottle;
  return function(...args) {
    if (!inThrottle) {
      func.apply(this, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  };
}

// ============================================
// ACCESSIBILITY ENHANCEMENTS
// ============================================

/**
 * Announce navigation changes to screen readers
 */
function announceNavigation(message) {
  const announcement = document.createElement('div');
  announcement.setAttribute('role', 'status');
  announcement.setAttribute('aria-live', 'polite');
  announcement.className = 'sr-only';
  announcement.textContent = message;
  
  document.body.appendChild(announcement);
  
  setTimeout(() => {
    announcement.remove();
  }, 1000);
}

// ============================================
// EXPORT FUNCTIONS
// ============================================

// Make functions available globally
window.toggleMobileMenu = toggleMobileMenu;
window.handleNavbarScroll = handleNavbarScroll;
window.updateActiveSection = updateActiveSection;
