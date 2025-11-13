{{-- Cookie Consent Banner --}}
<div id="cookieConsent" 
     class="cookie-consent"
     style="display: none;"
     role="dialog"
     aria-labelledby="cookieConsentTitle"
     aria-describedby="cookieConsentDesc">
    <div class="cookie-consent-container">
        <div class="cookie-consent-content">
            <div class="cookie-consent-icon">
                <i class="fas fa-cookie-bite"></i>
            </div>
            <div class="cookie-consent-text">
                <h3 id="cookieConsentTitle" class="cookie-consent-title">
                    🍪 Privasi & Cookie
                </h3>
                <p id="cookieConsentDesc" class="cookie-consent-description">
                    Kami menggunakan cookie dan teknologi pelacakan untuk meningkatkan pengalaman Anda, menganalisis lalu lintas website, dan memahami kebutuhan Anda. Data Anda akan dilindungi sesuai 
                    <a href="{{ route('privacy.policy') }}" class="cookie-link">Kebijakan Privasi</a> kami.
                </p>
                <div class="cookie-options">
                    <label class="cookie-option">
                        <input type="checkbox" checked disabled>
                        <span>Cookie Esensial</span>
                        <small>Diperlukan untuk fungsi website</small>
                    </label>
                    <label class="cookie-option">
                        <input type="checkbox" id="analyticsCookies" checked>
                        <span>Cookie Analitik</span>
                        <small>Membantu kami memahami penggunaan website</small>
                    </label>
                    <label class="cookie-option">
                        <input type="checkbox" id="marketingCookies">
                        <span>Cookie Marketing</span>
                        <small>Personalisasi konten dan iklan</small>
                    </label>
                </div>
            </div>
        </div>
        <div class="cookie-consent-actions">
            <button onclick="acceptAllCookies()" class="btn btn-accept">
                <i class="fas fa-check-circle"></i> Terima Semua
            </button>
            <button onclick="acceptEssentialOnly()" class="btn btn-essential">
                <i class="fas fa-shield-alt"></i> Hanya Esensial
            </button>
            <button onclick="savePreferences()" class="btn btn-save">
                <i class="fas fa-save"></i> Simpan Preferensi
            </button>
        </div>
    </div>
</div>

{{-- Cookie Preferences Button (Floating) --}}
<button id="cookiePreferences" 
        class="cookie-preferences-btn"
        onclick="showCookieConsent()"
        title="Pengaturan Cookie"
        aria-label="Buka pengaturan cookie">
    <i class="fas fa-cookie"></i>
</button>

<style>
/* Cookie Consent Banner */
.cookie-consent {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #1E40AF 0%, #1E3A8A 100%);
    color: white;
    padding: 1.5rem;
    box-shadow: 0 -10px 40px rgba(0,0,0,0.3);
    z-index: 9999;
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.cookie-consent-container {
    max-width: 1200px;
    margin: 0 auto;
}

.cookie-consent-content {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.cookie-consent-icon {
    font-size: 3rem;
    flex-shrink: 0;
    opacity: 0.9;
}

.cookie-consent-text {
    flex: 1;
}

.cookie-consent-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.cookie-consent-description {
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1rem;
    opacity: 0.95;
}

.cookie-link {
    color: #FCD34D;
    text-decoration: underline;
    font-weight: 600;
}

.cookie-link:hover {
    color: #FDE68A;
}

.cookie-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.cookie-option {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    cursor: pointer;
    padding: 0.75rem;
    background: rgba(255,255,255,0.1);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.cookie-option:hover {
    background: rgba(255,255,255,0.15);
}

.cookie-option input[type="checkbox"] {
    margin-right: 0.5rem;
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.cookie-option span {
    font-weight: 600;
    font-size: 0.95rem;
}

.cookie-option small {
    font-size: 0.85rem;
    opacity: 0.8;
    margin-left: 26px;
}

.cookie-consent-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
}

.cookie-consent-actions .btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-accept {
    background: #10B981;
    color: white;
}

.btn-accept:hover {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16,185,129,0.4);
}

.btn-essential {
    background: #F97316;
    color: white;
}

.btn-essential:hover {
    background: #EA580C;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(249,115,22,0.4);
}

.btn-save {
    background: rgba(255,255,255,0.2);
    color: white;
    border: 2px solid rgba(255,255,255,0.3);
}

.btn-save:hover {
    background: rgba(255,255,255,0.3);
    border-color: rgba(255,255,255,0.5);
    transform: translateY(-2px);
}

/* Cookie Preferences Floating Button */
.cookie-preferences-btn {
    position: fixed;
    bottom: 100px;
    right: 20px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);
    color: white;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(249,115,22,0.4);
    z-index: 998;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cookie-preferences-btn:hover {
    transform: translateY(-3px) rotate(15deg);
    box-shadow: 0 6px 20px rgba(249,115,22,0.6);
}

/* Responsive Design */
@media (max-width: 768px) {
    .cookie-consent {
        padding: 1rem;
    }
    
    .cookie-consent-content {
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .cookie-consent-icon {
        font-size: 2rem;
        text-align: center;
    }
    
    .cookie-consent-title {
        font-size: 1.25rem;
    }
    
    .cookie-consent-description {
        font-size: 0.875rem;
    }
    
    .cookie-options {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .cookie-consent-actions {
        flex-direction: column;
    }
    
    .cookie-consent-actions .btn {
        width: 100%;
        justify-content: center;
    }
    
    .cookie-preferences-btn {
        bottom: 80px;
        right: 15px;
        width: 45px;
        height: 45px;
        font-size: 1.25rem;
    }
}

/* Animation for hide */
@keyframes slideDown {
    from {
        transform: translateY(0);
        opacity: 1;
    }
    to {
        transform: translateY(100%);
        opacity: 0;
    }
}

.cookie-consent.hiding {
    animation: slideDown 0.3s ease-in forwards;
}
</style>

<script>
// Cookie Consent Management
(function() {
    'use strict';
    
    const COOKIE_NAME = 'bizmark_cookie_consent';
    const COOKIE_EXPIRY = 365; // days
    
    // Check if user has already made a choice
    function checkCookieConsent() {
        const consent = getCookie(COOKIE_NAME);
        if (!consent) {
            // Show banner after 1 second delay
            setTimeout(() => {
                document.getElementById('cookieConsent').style.display = 'block';
            }, 1000);
        } else {
            // Apply saved preferences
            applyConsent(JSON.parse(consent));
        }
    }
    
    // Accept all cookies
    window.acceptAllCookies = function() {
        const consent = {
            essential: true,
            analytics: true,
            marketing: true,
            timestamp: new Date().toISOString()
        };
        saveConsent(consent);
        enableAllTracking();
        hideBanner();
    };
    
    // Accept only essential cookies
    window.acceptEssentialOnly = function() {
        const consent = {
            essential: true,
            analytics: false,
            marketing: false,
            timestamp: new Date().toISOString()
        };
        saveConsent(consent);
        disableNonEssentialTracking();
        hideBanner();
    };
    
    // Save user preferences
    window.savePreferences = function() {
        const consent = {
            essential: true, // Always true
            analytics: document.getElementById('analyticsCookies').checked,
            marketing: document.getElementById('marketingCookies').checked,
            timestamp: new Date().toISOString()
        };
        saveConsent(consent);
        applyConsent(consent);
        hideBanner();
    };
    
    // Show cookie consent banner
    window.showCookieConsent = function() {
        const banner = document.getElementById('cookieConsent');
        banner.classList.remove('hiding');
        banner.style.display = 'block';
        
        // Load current preferences
        const consent = getCookie(COOKIE_NAME);
        if (consent) {
            const prefs = JSON.parse(consent);
            document.getElementById('analyticsCookies').checked = prefs.analytics;
            document.getElementById('marketingCookies').checked = prefs.marketing;
        }
    };
    
    // Hide banner with animation
    function hideBanner() {
        const banner = document.getElementById('cookieConsent');
        banner.classList.add('hiding');
        setTimeout(() => {
            banner.style.display = 'none';
            banner.classList.remove('hiding');
        }, 300);
    }
    
    // Save consent to cookie
    function saveConsent(consent) {
        setCookie(COOKIE_NAME, JSON.stringify(consent), COOKIE_EXPIRY);
    }
    
    // Apply consent settings
    function applyConsent(consent) {
        if (consent.analytics) {
            enableAnalytics();
        } else {
            disableAnalytics();
        }
        
        if (consent.marketing) {
            enableMarketing();
        } else {
            disableMarketing();
        }
    }
    
    // Enable all tracking
    function enableAllTracking() {
        enableAnalytics();
        enableMarketing();
        
        // Send consent event to GA4
        if (typeof gtag !== 'undefined') {
            gtag('event', 'cookie_consent', {
                consent_type: 'all',
                event_category: 'engagement',
                event_label: 'User accepted all cookies'
            });
        }
    }
    
    // Disable non-essential tracking
    function disableNonEssentialTracking() {
        disableAnalytics();
        disableMarketing();
        
        // Send consent event to GA4
        if (typeof gtag !== 'undefined') {
            gtag('event', 'cookie_consent', {
                consent_type: 'essential_only',
                event_category: 'engagement',
                event_label: 'User accepted essential cookies only'
            });
        }
    }
    
    // Enable Google Analytics
    function enableAnalytics() {
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted'
            });
        }
    }
    
    // Disable Google Analytics
    function disableAnalytics() {
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'denied'
            });
        }
    }
    
    // Enable marketing cookies
    function enableMarketing() {
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'ad_storage': 'granted',
                'ad_user_data': 'granted',
                'ad_personalization': 'granted'
            });
        }
    }
    
    // Disable marketing cookies
    function disableMarketing() {
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'ad_storage': 'denied',
                'ad_user_data': 'denied',
                'ad_personalization': 'denied'
            });
        }
    }
    
    // Cookie helper functions
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + date.toUTCString();
        const domain = window.location.hostname;
        const isSecure = window.location.protocol === 'https:';
        
        // Use domain for production, omit for localhost
        const domainAttr = domain.includes('localhost') || domain.includes('127.0.0.1') 
            ? '' 
            : ';domain=' + domain.replace('www.', '.');
        
        // Add Secure flag for HTTPS
        const secureAttr = isSecure ? ';Secure' : '';
        
        document.cookie = name + "=" + encodeURIComponent(value) + ";" + expires + ";path=/" + domainAttr + ";SameSite=Lax" + secureAttr;
        
        // Debug - check if cookie was set
        setTimeout(() => {
            const check = getCookie(name);
            console.log('Cookie set attempt:', name, 'Value saved:', check ? 'YES' : 'NO');
        }, 100);
    }
    
    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) {
                const value = c.substring(nameEQ.length, c.length);
                return decodeURIComponent(value);
            }
        }
        return null;
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', checkCookieConsent);
})();
</script>
