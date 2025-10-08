# Test Log

**Project:** Health Advice Group - Digital Solution Prototype
**Tester:** Gabriel
**Date:** 2025-10-08

This document outlines the iterative testing process undertaken to ensure the functionality, security, and usability of the prototype.

---

### Test Cycle 1: Core Feature Functionality

*   **Objective:** Verify that the basic features of the application are working as expected.
*   **Test Cases:**
    *   **Input:** Enter a valid city name (e.g., "London") into the Weather Forecast form.
        *   **Expected Outcome:** The page displays the current temperature, condition, humidity, and wind speed for London.
        *   **Actual Outcome:** Pass.
    *   **Input:** Enter a valid city name into the Air Quality form.
        *   **Expected Outcome:** The page displays the current air quality index and component data.
        *   **Actual Outcome:** Pass.
    *   **Input:** Enter a valid city name into the Health Advice form.
        *   **Expected Outcome:** The page displays relevant health advice based on the current weather and air quality.
        *   **Actual Outcome:** Pass.
    *   **Input:** Enter an invalid or non-existent city name (e.g., "InvalidCityName123").
        *   **Expected Outcome:** The page displays a user-friendly error message, such as "Could not find coordinates for the specified location."
        *   **Actual Outcome:** **Fail.** The page initially showed PHP warnings (`Undefined array key`).
        *   **Corrective Action:** Added checks to the PHP scripts to verify that the API returned valid data before attempting to access it. The page now correctly displays the expected error message.

---

### Test Cycle 2: User Authentication and Security

*   **Objective:** Test the user registration and login system, and verify that security measures are working.
*   **Test Cases:**
    *   **Input:** Register a new user with a unique email and a strong password.
        *   **Expected Outcome:** The user is created in the database, and the user is redirected to the login page with a success message.
        *   **Actual Outcome:** Pass.
    *   **Input:** Attempt to register with an email that already exists.
        *   **Expected Outcome:** The registration fails, and a clear error message ("Username or email already exists") is displayed.
        *   **Actual Outcome:** **Fail.** The initial implementation showed a generic "Registration failed" message.
        *   **Corrective Action:** Added specific error handling to the `register_process.php` script to check for duplicate entry errors from the database.
    *   **Input:** Log in with correct credentials.
        *   **Expected Outcome:** The user is redirected to the homepage, the navigation bar updates to show "Logout", and the "Personal Health Tracker" is visible.
        *   **Actual Outcome:** **Fail.** This was a persistent and complex issue. The login state was not being recognized after the redirect.
        *   **Corrective Action:** This required multiple iterations of debugging. The final, working solution was to implement a **URL-based session** system to bypass the user's restrictive browser environment that was blocking both cookies and `localStorage`. This involved manually rewriting all links and redirects to carry the session ID.
    *   **Input:** As a guest, attempt to access the `health-tracker.php` page directly.
        *   **Expected Outcome:** The user is immediately redirected to the `login.php` page.
        *   **Actual Outcome:** Pass.

---

### Test Cycle 3: Personal Health Tracker Functionality

*   **Objective:** Verify that logged-in users can manage their health logs.
*   **Test Cases:**
    *   **Input:** As a logged-in user, submit a new entry in the Personal Health Tracker form.
        *   **Expected Outcome:** The page reloads, and the new entry appears at the top of the "Your Health Log" table.
        *   **Actual Outcome:** Pass.
    *   **Security Test:** Log in as User A and note their user ID. Log in as User B. Attempt to view User A's health logs by manipulating the URL or API requests.
        *   **Expected Outcome:** The attempt fails. The application should only ever display the logs for the currently logged-in user.
        *   **Actual Outcome:** Pass. The implementation of **Row-Level Security (RLS)** in Supabase ensures that the database itself enforces this rule, providing a high level of security.

---

### Test Cycle 4: API and Environment Configuration

*   **Objective:** Test how the application handles configuration errors, particularly with external API keys.
*   **Test Cases:**
    *   **Input:** Temporarily remove or invalidate the `OPENWEATHERMAP_API_KEY` in the `.env` file.
        *   **Expected Outcome:** The Weather, Air Quality, and Health Advice pages load quickly and display a clear error message, such as "OpenWeatherMap API key is not configured."
        *   **Actual Outcome:** **Fail.** The pages would hang for a long time before timing out.
        *   **Corrective Action:** Added a connection and execution **timeout** to all external cURL requests. The pages now fail quickly and display the expected error message.
