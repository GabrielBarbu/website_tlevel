/**
 * Main JavaScript file for the Health Advice Group website.
 * This file can be used for any site-wide interactive features.
 */
document.addEventListener('DOMContentLoaded', () => {
    console.log('Health Advice Group website loaded.');

    // Hamburger menu functionality
    const hamburger = document.querySelector('.hamburger-menu');
    const navMenu = document.querySelector('#main-nav ul');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            const isExpanded = hamburger.getAttribute('aria-expanded') === 'true';
            navMenu.classList.toggle('nav-active');
            hamburger.setAttribute('aria-expanded', !isExpanded);
        });
    }
});
