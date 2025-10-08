<?php
/**
 * This script handles the user logout process.
 * It clears the user's authentication data from the session and redirects
 * to the homepage.
 */

// Include the database and session management files.
require_once 'includes/session.php';
require_once 'config/db.php';

// Call the sign-out function to clear the session variables.
supabaseAuthSignOut();

// Redirect the user to the homepage, ensuring the session ID is maintained
// so they are seen as a logged-out user.
header('Location: ' . session_url('index.php'));
exit(); // Terminate the script.
?>
