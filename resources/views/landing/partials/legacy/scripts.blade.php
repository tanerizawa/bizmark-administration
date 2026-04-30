    <!-- WhatsApp Floating Button (Desktop & adjusted on mobile) -->
    <a href="https://wa.me/6283879602855?text=Halo%20Bizmark.ID,%20saya%20ingin%20konsultasi%20tentang%20perizinan" 
       target="_blank" 
       rel="noopener"
       aria-label="Chat WhatsApp"
       class="wa-floating fixed bottom-6 right-6 w-16 h-16 rounded-full flex items-center justify-center shadow-lg transition-all z-50"
       style="background-color: var(--apple-green); animation: pulse 2s infinite;"
       onmouseover="this.style.transform='scale(1.1)'"
       onmouseout="this.style.transform='scale(1)'">
        <i class="fab fa-whatsapp text-white text-3xl" aria-hidden="true"></i>
    </a>

    <!-- Mobile Sticky CTA Bar (Thumb Zone - Bottom 1/3) -->
    <div class="mobile-sticky-cta" role="navigation" aria-label="Quick actions">
        <a href="/estimasi-biaya" class="cta-btn primary" data-neural-priority="highest">
            <i class="fas fa-calculator" aria-hidden="true"></i>
            <span>Estimasi Biaya</span>
        </a>
        <a href="tel:+6283879602855" class="cta-btn secondary">
            <i class="fas fa-phone" aria-hidden="true"></i>
            <span>Telepon</span>
        </a>
        <a href="https://wa.me/6283879602855?text=Halo%20Bizmark.ID" 
           target="_blank" 
           rel="noopener" 
           class="cta-btn whatsapp" 
           aria-label="WhatsApp">
            <i class="fab fa-whatsapp text-xl" aria-hidden="true"></i>
        </a>
    </div>

    <!-- Google Analytics (Replace with your GA4 Measurement ID) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-DT71N7BSW9"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-DT71N7BSW9');
    </script>

    <script>
        // Fix viewport height on mobile browsers (especially iOS Safari)
        function setVh() {
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        }
        
        setVh();
        window.addEventListener('resize', setVh);
        window.addEventListener('orientationchange', setVh);
        
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('active');
        }
        
        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth Scroll for Mobile Menu Links
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', function() {
                document.getElementById('mobileMenu').classList.remove('active');
            });
        });
        
        // ============================================
        // MULTI-STEP FORM WITH COGNITIVE LOAD OPTIMIZATION
        // ============================================
        
        let currentFormStep = 1;
        const formData = {};
        
        // Step Navigation
        function nextFormStep(step) {
            // Validate current step before proceeding
            if (!validateFormStep(currentFormStep)) {
                return;
            }
            
            // Save current step data
            saveFormStepData(currentFormStep);
            
            // Hide current step
            document.querySelector(`.form-step[data-step="${currentFormStep}"]`).style.display = 'none';
            
            // Mark current step as completed
            const currentProgressStep = document.querySelector(`.progress-step[data-step="${currentFormStep}"]`);
            currentProgressStep.classList.remove('active');
            currentProgressStep.classList.add('completed');
            currentProgressStep.innerHTML = '<i class="fas fa-check text-xs"></i>';
            
            // Mark progress line as completed
            if (currentFormStep < 3) {
                document.querySelectorAll('.progress-line')[currentFormStep - 1].classList.add('completed');
            }
            
            // Show next step
            currentFormStep = step;
            document.querySelector(`.form-step[data-step="${step}"]`).style.display = 'block';
            document.querySelector(`.progress-step[data-step="${step}"]`).classList.add('active');
            
            // If step 3, populate review
            if (step === 3) {
                populateReview();
            }
            
            // Scroll to form top
            document.getElementById('contactForm').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        function prevFormStep(step) {
            // Hide current step
            document.querySelector(`.form-step[data-step="${currentFormStep}"]`).style.display = 'none';
            document.querySelector(`.progress-step[data-step="${currentFormStep}"]`).classList.remove('active');
            
            // Show previous step
            currentFormStep = step;
            document.querySelector(`.form-step[data-step="${step}"]`).style.display = 'block';
            
            // Update progress indicators
            const prevProgressStep = document.querySelector(`.progress-step[data-step="${step}"]`);
            prevProgressStep.classList.remove('completed');
            prevProgressStep.classList.add('active');
            prevProgressStep.innerHTML = `<span class="text-xs font-bold">${step}</span>`;
            
            // Remove line completion
            if (step < 3) {
                document.querySelectorAll('.progress-line')[step - 1].classList.remove('completed');
            }
            
            // Scroll to form top
            document.getElementById('contactForm').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        // Form Validation (Real-time)
        function validateFormStep(step) {
            const stepElement = document.querySelector(`.form-step[data-step="${step}"]`);
            const fields = stepElement.querySelectorAll('input[required], textarea[required], select[required]');
            let isValid = true;
            
            fields.forEach(field => {
                const fieldContainer = field.closest('.form-field');
                const validation = field.dataset.validation;
                
                if (!validateField(field, validation)) {
                    isValid = false;
                    fieldContainer.classList.add('invalid');
                    fieldContainer.classList.remove('valid');
                } else {
                    fieldContainer.classList.add('valid');
                    fieldContainer.classList.remove('invalid');
                }
            });
            
            return isValid;
        }
        
        function validateField(field, rules) {
            const value = field.value.trim();
            const validationMessage = field.closest('.form-field').querySelector('.validation-message');
            
            if (!rules) return true;
            
            const ruleList = rules.split('|');
            
            for (let rule of ruleList) {
                if (rule === 'required' && !value) {
                    validationMessage.textContent = 'Field ini wajib diisi';
                    return false;
                }
                
                if (rule === 'email' && value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        validationMessage.textContent = 'Format email tidak valid';
                        return false;
                    }
                }
                
                if (rule === 'phone' && value) {
                    const phoneRegex = /^[0-9+\s-]{8,}$/;
                    if (!phoneRegex.test(value)) {
                        validationMessage.textContent = 'Format nomor telepon tidak valid';
                        return false;
                    }
                }
                
                if (rule.startsWith('min:')) {
                    const minLength = parseInt(rule.split(':')[1]);
                    if (value.length < minLength) {
                        validationMessage.textContent = `Minimal ${minLength} karakter`;
                        return false;
                    }
                }
            }
            
            validationMessage.textContent = 'Valid';
            return true;
        }
        
        // Real-time validation on input
        document.addEventListener('DOMContentLoaded', function() {
            const formInputs = document.querySelectorAll('#contactForm input, #contactForm textarea, #contactForm select');
            
            formInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    const validation = this.dataset.validation;
                    const fieldContainer = this.closest('.form-field');
                    
                    if (validateField(this, validation)) {
                        fieldContainer.classList.add('valid');
                        fieldContainer.classList.remove('invalid');
                    } else {
                        fieldContainer.classList.add('invalid');
                        fieldContainer.classList.remove('valid');
                    }
                });
                
                input.addEventListener('input', function() {
                    // Remove invalid state on typing
                    const fieldContainer = this.closest('.form-field');
                    if (fieldContainer.classList.contains('invalid')) {
                        fieldContainer.classList.remove('invalid');
                    }
                });
            });
        });
        
        // Save form step data
        function saveFormStepData(step) {
            const stepElement = document.querySelector(`.form-step[data-step="${step}"]`);
            const fields = stepElement.querySelectorAll('input, textarea, select');
            
            fields.forEach(field => {
                if (field.name) {
                    formData[field.name] = field.value;
                }
            });
        }
        
        // Populate review section
        function populateReview() {
            document.getElementById('reviewName').textContent = formData.name || '-';
            document.getElementById('reviewEmail').textContent = formData.email || '-';
            document.getElementById('reviewPhone').textContent = formData.phone || '-';
            
            // Get service label
            const serviceSelect = document.querySelector('select[name="service"]');
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            document.getElementById('reviewService').textContent = selectedOption.text || '-';
            
            document.getElementById('reviewMessage').textContent = formData.message || '-';
        }
        
        // Form Submission
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Save final step data
            saveFormStepData(3);
            
            // Track form submission with Google Analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'form_submit', {
                    'event_category': 'Contact',
                    'event_label': 'Multi-Step Contact Form',
                    'service_type': formData.service
                });
            }
            
            // Hide form steps, show success message
            document.querySelectorAll('.form-step').forEach(step => step.style.display = 'none');
            document.querySelector('.form-success').style.display = 'block';
            document.getElementById('formProgress').style.display = 'none';
            
            // Here you would normally send data to server via AJAX
            console.log('Form Data:', formData);
            
            // Scroll to success message
            document.querySelector('.form-success').scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
        
        // Reset form
        function resetContactForm() {
            // Reset all data
            Object.keys(formData).forEach(key => delete formData[key]);
            currentFormStep = 1;
            
            // Reset form
            document.getElementById('contactForm').reset();
            
            // Reset all validation states
            document.querySelectorAll('.form-field').forEach(field => {
                field.classList.remove('valid', 'invalid');
            });
            
            // Reset progress indicators
            document.querySelectorAll('.progress-step').forEach((step, index) => {
                step.classList.remove('active', 'completed');
                step.innerHTML = `<span class="text-xs font-bold">${index + 1}</span>`;
            });
            document.querySelector('.progress-step[data-step="1"]').classList.add('active');
            
            document.querySelectorAll('.progress-line').forEach(line => {
                line.classList.remove('completed');
            });
            
            // Show first step, hide success
            document.querySelectorAll('.form-step').forEach(step => step.style.display = 'none');
            document.querySelector('.form-step[data-step="1"]').style.display = 'block';
            document.querySelector('.form-success').style.display = 'none';
            document.getElementById('formProgress').style.display = 'flex';
        }
        
        // Form Submission (you can add AJAX here)
        // ... existing code removed as it's replaced by multi-step form handler above
        
        // Track CTA Button Clicks
        document.querySelectorAll('.btn-primary, .btn-secondary').forEach(button => {
            button.addEventListener('click', function() {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'button_click', {
                        'event_category': 'CTA',
                        'event_label': this.textContent.trim()
                    });
                }
            });
        });
        
        // Track WhatsApp Button Click
        document.querySelector('a[href*="wa.me"]').addEventListener('click', function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'whatsapp_click', {
                    'event_category': 'Contact',
                    'event_label': 'WhatsApp Floating Button'
                });
            }
        });
        
        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('✅ Service Worker registered:', registration.scope);
                    })
                    .catch(error => {
                        console.error('❌ Service Worker registration failed:', error);
                    });
            });
        }
        
        // PWA Install Prompt
        let deferredPrompt;
        let installPromptShown = localStorage.getItem('installPromptShown') === 'true';
        let installPromptDismissed = localStorage.getItem('installPromptDismissed');
        
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Check if prompt was recently dismissed (within 7 days)
            const dismissedDate = installPromptDismissed ? new Date(installPromptDismissed) : null;
            const daysSinceDismissed = dismissedDate ? (Date.now() - dismissedDate.getTime()) / (1000 * 60 * 60 * 24) : 999;
            
            // Show prompt if:
            // 1. Never shown before, OR
            // 2. Was dismissed more than 7 days ago, AND
            // 3. User has been on site for at least 30 seconds
            if (!installPromptShown || daysSinceDismissed > 7) {
                setTimeout(() => {
                    if (window.scrollY > 200 || document.hidden === false) {
                        showInstallPrompt();
                    }
                }, 30000); // Show after 30 seconds
            }
        });
        
        function showInstallPrompt() {
            // Create install banner
            const banner = document.createElement('div');
            banner.id = 'pwa-install-banner';
            banner.style.cssText = `
                position: fixed;
                bottom: var(--spacing-xl);
                left: var(--spacing-xl);
                right: var(--spacing-xl);
                background: var(--gradient-apple-blue);
                color: var(--color-text-white);
                padding: var(--spacing-xl);
                border-radius: var(--radius-xl);
                box-shadow: var(--shadow-primary-xl);
                z-index: 9999;
                display: flex;
                align-items: center;
                gap: var(--spacing-md);
                animation: slideUp 0.3s ease-out;
                max-width: 500px;
                margin: 0 auto;
            `;
            
            banner.innerHTML = `
                <div style="font-size: 32px;">📱</div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; font-size: 16px; margin-bottom: 4px;">Install Bizmark.ID</div>
                    <div style="font-size: 13px; opacity: 0.9;">Akses lebih cepat dengan install aplikasi di home screen</div>
                </div>
                <button id="install-btn" style="
                    background: var(--color-text-white);
                    color: var(--apple-blue);
                    border: none;
                    padding: var(--spacing-sm) var(--spacing-xl);
                    border-radius: var(--radius-md);
                    font-weight: var(--font-weight-semibold);
                    cursor: pointer;
                    font-size: var(--font-size-sm);
                    white-space: nowrap;
                ">Install</button>
                <button id="dismiss-btn" style="
                    background: transparent;
                    color: var(--color-text-white);
                    border: 1px solid var(--color-border-tertiary);
                    padding: var(--spacing-sm) var(--spacing-md);
                    border-radius: var(--radius-md);
                    cursor: pointer;
                    font-size: var(--font-size-xl);
                ">✕</button>
            `;
            
            document.body.appendChild(banner);
            
            // Install button handler
            document.getElementById('install-btn').addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    
                    console.log(`PWA install outcome: ${outcome}`);
                    
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'pwa_install_prompt', {
                            'event_category': 'PWA',
                            'event_label': outcome
                        });
                    }
                    
                    localStorage.setItem('installPromptShown', 'true');
                    deferredPrompt = null;
                    banner.remove();
                }
            });
            
            // Dismiss button handler
            document.getElementById('dismiss-btn').addEventListener('click', () => {
                banner.remove();
                localStorage.setItem('installPromptDismissed', new Date().toISOString());
                
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'pwa_install_dismissed', {
                        'event_category': 'PWA'
                    });
                }
            });
        }
        
        // Track PWA installation
        window.addEventListener('appinstalled', () => {
            console.log('✅ PWA installed successfully!');
            
            if (typeof gtag !== 'undefined') {
                gtag('event', 'pwa_installed', {
                    'event_category': 'PWA'
                });
            }
        });
        
        // Screen Width Detection (Auto-redirect disabled to prevent infinite loop)
        // Users can manually switch using the toggle in header
        function updateScreenWidthDesktop() {
            const width = window.innerWidth;
            
            // Send width to server for analytics
            fetch('/api/set-screen-width', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ width: width })
            }).catch(err => console.log('Screen width update failed:', err));
        }
        
        // Update on load
        updateScreenWidthDesktop();
        
        // Update on resize (debounced)
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(updateScreenWidthDesktop, 1000);
        });
        
        // Add slide up animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(100px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
