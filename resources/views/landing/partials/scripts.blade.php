<script>
// Landing Page Scripts (non-module to ensure onclick handlers work globally)
// navbar.js features are bundled via Vite in app.js

// Hide Loading Screen
window.addEventListener('load', function() {
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen) {
        setTimeout(() => {
            loadingScreen.classList.add('hidden');
        }, 500);
    }
});

// Mobile Menu Toggle
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    if (!menu) {
        return;
    }

    const menuButton = document.getElementById('mobile-menu-button');
    const backdrop = menu.querySelector('.mobile-menu-backdrop');
    const panel = menu.querySelector('.mobile-menu-panel');
    const isHidden = menu.classList.contains('hidden');

    if (isHidden) {
        // Open: show container, then animate in
        menu.classList.remove('hidden');
        // Force reflow so transitions trigger
        menu.offsetHeight;
        menu.classList.add('active');
        if (backdrop) backdrop.classList.add('active');
        if (panel) panel.classList.add('active');
        document.body.style.overflow = 'hidden';
        if (menuButton) {
            menuButton.setAttribute('aria-expanded', 'true');
        }
        // Trap focus inside menu
        const firstFocusable = menu.querySelector('button, a, input');
        if (firstFocusable) firstFocusable.focus();
        // Haptic feedback
        if (navigator.vibrate) navigator.vibrate(10);
    } else {
        // Close: animate out, then hide container
        menu.classList.remove('active');
        if (backdrop) backdrop.classList.remove('active');
        if (panel) panel.classList.remove('active');
        document.body.style.overflow = '';
        if (menuButton) {
            menuButton.setAttribute('aria-expanded', 'false');
            menuButton.focus();
        }
        // Wait for transition to finish before hiding
        setTimeout(function() {
            if (!menu.classList.contains('active')) {
                menu.classList.add('hidden');
            }
        }, 300);
        if (navigator.vibrate) navigator.vibrate(10);
    }
}

// Expose for inline onclick handlers
window.toggleMobileMenu = toggleMobileMenu;

// Close mobile menu when clicking overlay
document.addEventListener('click', function(e) {
    const menu = document.getElementById('mobileMenu');
    const menuButton = document.getElementById('mobile-menu-button');

    if (menu && menu.classList.contains('active') && 
        !menu.contains(e.target) && 
        menuButton && !menuButton.contains(e.target)) {
        toggleMobileMenu();
    }
});

// Close mobile menu on ESC key
document.addEventListener('keydown', function(e) {
    const menu = document.getElementById('mobileMenu');
    if (menu && e.key === 'Escape' && menu.classList.contains('active')) {
        toggleMobileMenu();
    }
    // Close locale dropdown on ESC
    const localeDropdown = document.getElementById('localeDropdown');
    if (localeDropdown && e.key === 'Escape') {
        localeDropdown.classList.add('hidden');
    }
    // Close tools dropdown on ESC
    const toolsMenu = document.getElementById('toolsMenu');
    if (toolsMenu && e.key === 'Escape') {
        toolsMenu.classList.add('hidden');
    }
});

// Close locale dropdown when clicking outside
document.addEventListener('click', function(e) {
    const switcher = document.getElementById('localeSwitcher');
    const dropdown = document.getElementById('localeDropdown');
    if (switcher && dropdown && !switcher.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
    // Close tools dropdown when clicking outside
    const toolsDropdown = document.getElementById('toolsDropdown');
    const toolsMenu = document.getElementById('toolsMenu');
    if (toolsDropdown && toolsMenu && !toolsDropdown.contains(e.target)) {
        toolsMenu.classList.add('hidden');
    }
});

// Back to Top Button
const backToTopBtn = document.getElementById('backToTop');
window.addEventListener('scroll', function() {
    if (!backToTopBtn) {
        return;
    }
    if (window.scrollY > 500) {
        backToTopBtn.classList.add('show');
    } else {
        backToTopBtn.classList.remove('show');
    }
});

// Navbar Scroll Effect
const navbar = document.querySelector('nav[role="navigation"]');
window.addEventListener('scroll', function() {
    if (!navbar) {
        return;
    }
    if (window.scrollY > 50) {
        navbar.classList.add('shadow-md');
        navbar.classList.remove('shadow-sm');
        navbar.style.background = 'rgba(255,255,255,0.98)';
    } else {
        navbar.classList.remove('shadow-md');
        navbar.classList.add('shadow-sm');
        navbar.style.background = '';
    }
});

// Smooth Scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                // Close mobile menu if open
                const mobileMenu = document.getElementById('mobileMenu');
                if (mobileMenu && mobileMenu.classList.contains('active')) {
                    toggleMobileMenu();
                }
            }
        }
    });
});

// Cookie Consent
function checkCookieConsent() {
    const consent = localStorage.getItem('cookieConsent');
    if (!consent) {
        const banner = document.getElementById('cookieConsent');
        if (banner) {
            banner.style.display = 'block';
            setTimeout(() => {
                banner.classList.remove('translate-y-full');
            }, 100);
        }
    }
}

function acceptCookies() {
    localStorage.setItem('cookieConsent', 'accepted');
    hideCookieBanner();
}

function rejectCookies() {
    localStorage.setItem('cookieConsent', 'rejected');
    hideCookieBanner();
}

function hideCookieBanner() {
    const banner = document.getElementById('cookieConsent');
    if (banner) {
        banner.classList.add('translate-y-full');
        setTimeout(() => {
            banner.style.display = 'none';
        }, 500);
    }
}

// Check cookie consent on load
window.addEventListener('DOMContentLoaded', function() {
    checkCookieConsent();
});

// Track events (placeholder)
function trackEvent(category, action, label, value = null) {
    if (localStorage.getItem('cookieConsent') === 'accepted') {
        // Send to Google Analytics 4
        if (typeof gtag !== 'undefined') {
            gtag('event', action, {
                'event_category': category,
                'event_label': label,
                'value': value
            });
        }
    }
}

// Expose for inline onclick handlers
window.trackEvent = trackEvent;

// Track WhatsApp clicks
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const phoneNumber = this.href.match(/(\d+)/)?.[0] || 'unknown';
            trackEvent('Engagement', 'whatsapp_click', 'WhatsApp Chat', phoneNumber);
            
            // Track conversion event
            if (typeof gtag !== 'undefined') {
                gtag('event', 'conversion', {
                    'send_to': 'AW-CONVERSION_ID/CONVERSION_LABEL'
                });
            }
        });
    });
    
    // Track phone clicks
    document.querySelectorAll('a[href^="tel:"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const phoneNumber = this.href.replace('tel:', '');
            trackEvent('Engagement', 'phone_click', 'Phone Call', phoneNumber);
        });
    });
    
    // Track email clicks
    document.querySelectorAll('a[href^="mailto:"]').forEach(link => {
        link.addEventListener('click', function() {
            trackEvent('Engagement', 'email_click', 'Email Contact');
        });
    });
    
    // Track CTA clicks
    document.querySelectorAll('.btn, a[data-cta]').forEach(button => {
        button.addEventListener('click', function() {
            const buttonText = this.textContent.trim();
            const ctaId = this.dataset.cta || buttonText;
            const ctaLocation = this.closest('section')?.id || 'unknown';
            trackEvent('CTA', 'button_click', ctaId, ctaLocation);
        });
    });
    
    // Track form submissions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const formName = this.id || this.action || 'contact_form';
            trackEvent('Form', 'form_submit', formName);
            
            // Mark form as submitted to prevent abandonment tracking
            this.dataset.submitted = 'true';
            
            // Track conversion
            if (typeof gtag !== 'undefined') {
                gtag('event', 'generate_lead', {
                    'event_category': 'Lead Generation',
                    'event_label': formName
                });
            }
        });
    });
    
    // Track scroll depth
    let scrollDepthTracked = {
        25: false,
        50: false,
        75: false,
        100: false
    };
    
    window.addEventListener('scroll', function() {
        const scrollPercent = Math.round((window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100);
        
        Object.keys(scrollDepthTracked).forEach(depth => {
            if (scrollPercent >= depth && !scrollDepthTracked[depth]) {
                scrollDepthTracked[depth] = true;
                trackEvent('Engagement', 'scroll_depth', depth + '%', scrollPercent);
            }
        });
    });
    
    // Track time on page
    let timeOnPage = 0;
    const timeTracking = setInterval(() => {
        timeOnPage += 10;
        
        // Track milestones
        if (timeOnPage === 30) {
            trackEvent('Engagement', 'time_on_page', '30_seconds', 30);
        } else if (timeOnPage === 60) {
            trackEvent('Engagement', 'time_on_page', '1_minute', 60);
        } else if (timeOnPage === 180) {
            trackEvent('Engagement', 'time_on_page', '3_minutes', 180);
        }
    }, 10000); // Every 10 seconds
    
    // Track page exit
    window.addEventListener('beforeunload', function() {
        trackEvent('Engagement', 'page_exit', 'Time on page', timeOnPage);
        clearInterval(timeTracking);
    });
    
    // Track service clicks
    document.querySelectorAll('a[href*="/layanan/"]').forEach(link => {
        link.addEventListener('click', function() {
            const serviceName = this.href.split('/').pop();
            trackEvent('Services', 'service_click', serviceName);
        });
    });
    
    // Track blog article clicks
    document.querySelectorAll('a[href*="/blog/"]').forEach(link => {
        link.addEventListener('click', function() {
            const articleSlug = this.href.split('/').pop();
            trackEvent('Content', 'article_click', articleSlug);
        });
    });
    
    // Track download clicks (future use)
    document.querySelectorAll('a[href*="/download/"], a[download]').forEach(link => {
        link.addEventListener('click', function() {
            const fileName = this.href.split('/').pop() || this.download;
            trackEvent('Downloads', 'file_download', fileName);
        });
    });
    
    // Track video plays (if videos exist)
    document.querySelectorAll('video').forEach(video => {
        video.addEventListener('play', function() {
            trackEvent('Media', 'video_play', video.src || 'unknown');
        });
        
        video.addEventListener('ended', function() {
            trackEvent('Media', 'video_complete', video.src || 'unknown');
        });
    });
    
    // Track outbound links
    document.querySelectorAll('a[href^="http"]').forEach(link => {
        if (!link.href.includes(window.location.hostname)) {
            link.addEventListener('click', function() {
                trackEvent('Outbound', 'external_link', this.href);
            });
        }
    });
    
    // Track search (if search functionality exists)
    document.querySelectorAll('input[type="search"], input[name="search"], input[name="q"]').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.value) {
                trackEvent('Search', 'search_query', this.value);
            }
        });
    });

    // FAQ accordion
    const faqButtons = document.querySelectorAll('.faq-trigger');
    const toggleFaq = (button, expand) => {
        const targetId = button.getAttribute('data-faq-target');
        const target = targetId ? document.getElementById(targetId) : null;
        const icon = button.querySelector('.faq-icon');
        const faqItem = button.closest('.faq-item');

        if (!target) {
            return;
        }

        if (expand) {
            button.setAttribute('aria-expanded', 'true');
            target.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-180');
            if (faqItem) faqItem.classList.add('faq-item-open');
        } else {
            button.setAttribute('aria-expanded', 'false');
            target.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-180');
            if (faqItem) faqItem.classList.remove('faq-item-open');
        }
    };

    faqButtons.forEach(button => {
        button.addEventListener('click', () => {
            const isExpanded = button.getAttribute('aria-expanded') === 'true';

            faqButtons.forEach(otherButton => {
                if (otherButton !== button) {
                    toggleFaq(otherButton, false);
                }
            });

            toggleFaq(button, !isExpanded);
        });
    });
    
    // Device Detection (Auto-redirect disabled to prevent infinite loop)
    // Users can manually switch using the view toggle in header
    
    // Screen width tracking removed - unnecessary HTTP requests
});
</script>
