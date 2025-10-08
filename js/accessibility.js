/**
 * Accessibility Settings Manager
 *
 * This script reads accessibility preferences from localStorage and applies them
 * to the document by adding classes to the body element.
 */

// Create a global function to be called from the settings page or on page load.
window.applyAccessibilitySettings = (settings) => {
    const body = document.body;

    // Apply High Contrast mode
    if (settings && settings.highContrast) {
        body.classList.add('high-contrast');
    } else {
        body.classList.remove('high-contrast');
    }

    // Apply Large Font Size
    if (settings && settings.largeFont) {
        body.classList.add('large-font');
    } else {
        body.classList.remove('large-font');
    }
};

// Apply saved settings on every page load
document.addEventListener('DOMContentLoaded', () => {
    const savedSettings = JSON.parse(localStorage.getItem('accessibility_settings'));
    if (savedSettings) {
        window.applyAccessibilitySettings(savedSettings);
    }
});
