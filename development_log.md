# Development Log

**Project:** Health Advice Group - Digital Solution Prototype
**Developer:** [Your Name]
**Date:** 2025-10-08

This document outlines the iterative development process undertaken to create the functional prototype.

---

### Iteration 1: Initial Scaffolding and Feature Implementation

*   **Objective:** Create the basic file structure and implement the core features as separate pages.
*   **Activities:**
    *   Created the initial project structure with PHP files for each main feature (`index.php`, `weather.php`, `air-quality.php`, `health-advice.php`).
    *   Set up a basic HTML structure with a shared header and footer (`includes/header.php`, `includes/footer.php`).
    *   Implemented a simple user system with `login.php`, `register.php`, and a custom `users` table in Supabase.
    *   Integrated the OpenWeatherMap API to fetch weather and air quality data.
    *   Created a basic stylesheet (`css/style.css`) for initial layout.
*   **Outcome:** A functional but basic prototype with separate pages for each feature. The UI was minimal, and security was not yet a primary focus.

---

### Iteration 2: UI/UX Redesign and Dashboard Implementation

*   **Objective:** Improve the user experience by creating a modern, professional design and a central dashboard.
*   **Activities:**
    *   Completely redesigned the CSS, introducing a modern color scheme, improved typography with Google Fonts, and a consistent "card"-based layout.
    *   Refactored `index.php` to serve as a central dashboard, providing clear navigation to all key features.
    *   Updated the header to include a dynamic navigation bar that changes based on the user's login status.
    *   Applied the new card-based design to all feature pages for a consistent look and feel.
*   **Outcome:** A visually appealing and user-friendly prototype. The dashboard significantly improved navigation and usability.

---

### Iteration 3: Security Enhancement and Backend Refactoring

*   **Objective:** Address major security vulnerabilities and align the application with development best practices.
*   **Activities:**
    *   Replaced the insecure plain-text password system with `password_hash()` and `password_verify()`.
    *   Moved all sensitive credentials (API keys, database URLs) from the source code into a `.env` file to prevent them from being committed to version control.
    *   Created a `load_env.php` script to securely load these credentials.
    *   Added the `.env` file to `.gitignore`.
*   **Outcome:** A significantly more secure application, following standard security practices for handling passwords and API keys.

---

### Iteration 4: Implementation of Advanced Features

*   **Objective:** Add the remaining features requested by the client: location-based personalization and a personal health tracker.
*   **Activities:**
    *   Implemented a "Use My Location" feature using the browser's Geolocation API, allowing for personalized, location-aware weather and health advice.
    *   Updated the backend PHP scripts to handle both city names and geographic coordinates.
    *   Created the `health-tracker.php` page for logged-in users.
    *   Designed and created the `health_logs` table in the database to store user-specific health data.
    *   Implemented the `add_health_entry.php` script to handle the submission of new health logs.
*   **Outcome:** The prototype now included all the core and advanced features outlined in the project brief.

---

### Iteration 5: Migration to Supabase's Built-in Authentication

*   **Objective:** Further enhance security and scalability by migrating from a custom user management system to Supabase's official authentication service.
*   **Activities:**
    *   Created a new `profiles` table for public user data, linked to Supabase's secure `auth.users` table.
    *   Implemented a database trigger (`handle_new_user`) to automatically create a profile when a new user signs up.
    *   Enabled and configured **Row-Level Security (RLS)** on the `health_logs` table to ensure users can only access their own private data.
    *   Refactored the entire PHP backend to use the Supabase Auth API for registration and login, removing the old, insecure `users` table.
    *   Updated the application to use email for login, as is standard with Supabase Auth.
*   **Outcome:** A highly secure and robust application, leveraging the full power of the backend service and following modern security best practices.

---

### Iteration 6: Debugging and Environment-Specific Fixes

*   **Objective:** Diagnose and fix a persistent login issue caused by the user's highly restricted local development environment.
*   **Activities:**
    *   Conducted extensive iterative testing on the session handling mechanism, diagnosing that both cookies and `localStorage` were being blocked by the browser.
    *   Refactored the entire application to use a **URL-based session** system, which does not rely on cookies or `localStorage`.
    *   Created a `session_url()` helper function to manually append the session ID to every link, form, and redirect.
    *   Diagnosed and fixed a fatal memory exhaustion error caused by a conflict in PHP's session handling settings.
*   **Outcome:** A fully functional prototype that works correctly even in a highly restrictive, cookie-less environment. The application is now stable and robust.
