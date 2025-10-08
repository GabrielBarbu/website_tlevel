<?php
/**
 * Handles user registration.
 * This script processes the registration form submission, creates a new user
 * account via the Supabase Auth service, and redirects the user accordingly.
 */
require_once 'includes/session.php';
require_once 'config/db.php';

// Process the form only if the request method is POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user details from the form submission.
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Attempt to sign up the new user using the Supabase Auth function.
    $response = supabaseAuthSignUp($email, $password, $username);

    // Check if the registration was successful by looking for a user ID in the response.
    if (isset($response['user']['id'])) {
        // Registration successful.
        // Redirect to the login page with a success message.
        header('Location: ' . session_url('login.php?registration=success'));
        exit();
    } else {
        // Handle registration error.
        $errorMessage = "Registration failed. Please try again.";
        // Provide a more specific error from Supabase if available.
        if (isset($response['msg'])) {
            $errorMessage = "Supabase Error: " . $response['msg'];
        }
        // Redirect back to the registration page with an error message.
        header('Location: ' . session_url('register.php?error=' . urlencode($errorMessage)));
        exit();
    }
}
?>
