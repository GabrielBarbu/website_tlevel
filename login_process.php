<?php
/**
 * Handles user login authentication.
 * This script processes the login form submission, verifies credentials against
 * the Supabase Auth service, and creates a user session upon success.
 */
require_once 'config/db.php';
require_once 'includes/session.php';

// Process the form only if the request method is POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get email and password from the form submission.
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Attempt to sign in the user using the Supabase Auth function.
    $response = supabaseAuthSignIn($email, $password);

    // Check if the login was successful by looking for an access token in the response.
    if (isset($response['access_token'])) {
        // Login successful.
        session_regenerate_id(true); // Regenerate session ID for security.
        
        // Store user token and ID in the session.
        $_SESSION['user_token'] = $response['access_token'];
        $_SESSION['user_id'] = $response['user']['id'];
        
        // Explicitly save the session data before redirecting.
        session_write_close();

        // Redirect to the home page, appending the session ID to the URL.
        header('Location: ' . session_url('index.php'));
        exit();
    } else {
        // Handle login error.
        $errorMessage = "Invalid email or password.";
        // Provide a more specific error from Supabase if available.
        if (isset($response['error_description'])) {
            $errorMessage = "Login Error: " . $response['error_description'];
        }
        // Redirect back to the login page with an error message.
        header('Location: ' . session_url('login.php?error=' . urlencode($errorMessage)));
        exit();
    }
}
?>
