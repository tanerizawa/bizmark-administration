import './bootstrap';

// Import self-hosted libraries
import Alpine from 'alpinejs';
import AOS from 'aos';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Initialize AOS (Animate On Scroll)
AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true,
    offset: 100
});
