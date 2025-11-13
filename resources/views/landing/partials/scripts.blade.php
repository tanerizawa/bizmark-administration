<!-- AOS Animation -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>

<!-- Alpine.js for dropdowns -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
// Initialize AOS
AOS.init({
    duration: 1000,
    once: true,
    offset: 100
});

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
    const menuButton = document.getElementById('mobile-menu-button');
    const isActive = menu.classList.toggle('active');
    document.body.classList.toggle('mobile-menu-open');
    
    // Update aria-expanded attribute for accessibility
    if (menuButton) {
        menuButton.setAttribute('aria-expanded', isActive ? 'true' : 'false');
        menuButton.setAttribute('aria-label', isActive ? 'Close navigation menu' : 'Open navigation menu');
    }
    
    // Trap focus inside menu when open
    if (isActive) {
        const focusableElements = menu.querySelectorAll('a, button');
        if (focusableElements.length > 0) {
            focusableElements[0].focus();
        }
    }
}

// Close mobile menu when clicking overlay
document.addEventListener('click', function(e) {
    const menu = document.getElementById('mobileMenu');
    const menuButton = document.getElementById('mobile-menu-button');
    
    if (menu.classList.contains('active') && 
        !menu.contains(e.target) && 
        menuButton && !menuButton.contains(e.target)) {
        toggleMobileMenu();
    }
});

// Close mobile menu on ESC key
document.addEventListener('keydown', function(e) {
    const menu = document.getElementById('mobileMenu');
    if (e.key === 'Escape' && menu.classList.contains('active')) {
        toggleMobileMenu();
    }
});

// Back to Top Button
const backToTopBtn = document.getElementById('backToTop');
window.addEventListener('scroll', function() {
    if (window.scrollY > 500) {
        backToTopBtn.classList.add('show');
    } else {
        backToTopBtn.classList.remove('show');
    }
});

// Navbar Scroll Effect
const navbar = document.querySelector('.navbar');
window.addEventListener('scroll', function() {
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Smooth Scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href !== '#contact') {
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
    console.log('Cookies accepted');
}

function rejectCookies() {
    localStorage.setItem('cookieConsent', 'rejected');
    hideCookieBanner();
    console.log('Cookies rejected');
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
        console.log('Event:', category, action, label, value);
        
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

// Track WhatsApp clicks
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const phoneNumber = this.href.match(/(\d+)/)?.[0] || 'unknown';
            trackEvent('Engagement', 'whatsapp_click', 'WhatsApp Chat', phoneNumber);
            
            // Track conversion event
            if (typeof gtag !== 'undefined') {
                gtag('event', 'conversion', {
                    'send_to': 'AW-CONVERSION_ID/CONVERSION_LABEL',
                    'event_callback': function() {
                        console.log('Conversion tracked');
                    }
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
});
</script>
