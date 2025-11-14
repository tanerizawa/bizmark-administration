import './bootstrap';

// Import self-hosted libraries
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import AOS from 'aos';

// Register Alpine plugins
Alpine.plugin(collapse);

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
