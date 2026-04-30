<script>
// Landing Page Scripts
// Dropdown system handled by Alpine.js in navbar.blade.php
// Mobile menu handled by Alpine.js in mobile-menu.blade.php
// FAQ accordion now uses native <details> elements

// Hide Loading Screen
window.addEventListener('load', function() {
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen) {
        setTimeout(() => {
            loadingScreen.classList.add('hidden');
        }, 500);
    }
});

// Back to Top button — show after 500px scroll
(function() {
    var backToTopBtn = document.getElementById('backToTop');
    if (!backToTopBtn) return;
    window.addEventListener('scroll', function() {
        backToTopBtn.classList.toggle('show', window.scrollY > 500);
    }, { passive: true });
})();

// Scroll Progress Bar
(function(){
    const bar = document.createElement('div');
    bar.id = 'scroll-progress';
    bar.setAttribute('aria-hidden', 'true');
    document.body.prepend(bar);
    const updateProgress = function() {
        const scrolled = window.scrollY;
        const total = document.documentElement.scrollHeight - window.innerHeight;
        bar.style.width = total > 0 ? ((scrolled / total) * 100).toFixed(2) + '%' : '0%';
    };
    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();
})();

// Navbar Scroll Effect — CSS class driven
const navbar = document.querySelector('nav[role="navigation"]');
const _updateNavbar = function() {
    if (!navbar) return;
    if (window.scrollY > 50) {
        navbar.classList.add('is-scrolled');
        navbar.style.background = '';
    } else {
        navbar.classList.remove('is-scrolled');
    }
};
window.addEventListener('scroll', _updateNavbar, { passive: true });
_updateNavbar();

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

// Expose for Alpine.js @click handlers
window.trackEvent = trackEvent;

// ── Dark/Light Mode Toggle ─────────────────────────────────────────────────
(function() {
    var themeIcon = document.getElementById('themeIcon');
    function updateThemeIcon(theme) {
        if (!themeIcon) return;
        themeIcon.className = 'fas ' + (theme === 'light' ? 'fa-sun' : 'fa-moon');
    }
    // Set icon based on current theme
    var currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
    updateThemeIcon(currentTheme);
})();

window.toggleTheme = function() {
    var html = document.documentElement;
    var isLight = html.getAttribute('data-theme') === 'light';
    var newTheme = isLight ? 'dark' : 'light';
    if (newTheme === 'light') {
        html.setAttribute('data-theme', 'light');
    } else {
        html.removeAttribute('data-theme');
    }
    localStorage.setItem('bizmark_theme', newTheme);
    // Update icon
    var icon = document.getElementById('themeIcon');
    if (icon) {
        icon.className = 'fas ' + (newTheme === 'light' ? 'fa-sun' : 'fa-moon');
    }
    // Dispatch event for AOS refresh
    window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }));
};

// ── Analytics / Tracking ────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Track WhatsApp clicks
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
});
</script>
