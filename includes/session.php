<?php
/**
 * Session Management for Cookie-less Environments
 *
 * This script ensures that sessions work correctly even if cookies are disabled
 * by passing the session ID in the URL. It starts the session and provides a helper
 * function to append the session ID to URLs.
 */

// Definitive session fix for cookie-less environments.
if (session_status() === PHP_SESSION_NONE) {
    // We are manually managing the session ID in the URL, so we disable
    // PHP's automatic cookie and URL rewriting features to prevent conflicts.
    ini_set('session.use_cookies', '0');
    ini_set('session.use_only_cookies', '0');
    ini_set('session.use_trans_sid', '0'); // Disable automatic rewriting.
    
    // Manually retrieve the session ID from the URL query string if it exists.
    if (isset($_GET[session_name()])) {
        session_id($_GET[session_name()]);
    }

    // Start or resume the session.
    session_start();
}

/**
 * Appends the session ID to a URL for cookie-less session tracking.
 *
 * @param string $url The URL to modify.
 * @return string The URL with the session ID appended as a query parameter.
 */
function session_url($url) {
    // Determine the correct separator ('?' or '&') based on the URL structure.
    $separator = (strpos($url, '?') === false) ? '?' : '&';
    // Append the session name and ID to the URL.
    return $url . $separator . session_name() . '=' . session_id();
}
?>
