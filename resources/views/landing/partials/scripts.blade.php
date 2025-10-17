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
    menu.classList.toggle('active');
    document.body.classList.toggle('mobile-menu-open');
}

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
function trackEvent(category, action, label) {
    if (localStorage.getItem('cookieConsent') === 'accepted') {
        console.log('Event:', category, action, label);
        // Add your analytics here
    }
}

// Track WhatsApp clicks
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href*="wa.me"]').forEach(link => {
        link.addEventListener('click', function() {
            trackEvent('Engagement', 'whatsapp_click', 'WhatsApp Chat');
        });
    });
    
    // Track phone clicks
    document.querySelectorAll('a[href^="tel:"]').forEach(link => {
        link.addEventListener('click', function() {
            trackEvent('Engagement', 'phone_click', 'Phone Call');
        });
    });
    
    // Track CTA clicks
    document.querySelectorAll('.btn-primary, .btn-secondary').forEach(button => {
        button.addEventListener('click', function() {
            const buttonText = this.textContent.trim();
            trackEvent('CTA', 'button_click', buttonText);
        });
    });

    // FAQ accordion
    const faqButtons = document.querySelectorAll('.faq-trigger');
    const toggleFaq = (button, expand) => {
        const targetId = button.getAttribute('data-faq-target');
        const target = targetId ? document.getElementById(targetId) : null;
        const icon = button.querySelector('.faq-icon');

        if (!target) {
            return;
        }

        if (expand) {
            button.setAttribute('aria-expanded', 'true');
            target.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-180');
        } else {
            button.setAttribute('aria-expanded', 'false');
            target.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-180');
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
