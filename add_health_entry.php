<?php
/**
 * Handles the submission of a new health log entry.
 * This script processes the form data from health-tracker.php,
 * validates the user's session, and inserts the new entry into the database.
 */
require_once 'includes/session.php';
require_once 'config/db.php';

// Redirect to login page if the user is not logged in.
if (!isLoggedIn()) {
    header('Location: ' . session_url('login.php'));
    exit();
}

// Process the form only if the request method is POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user and form data.
    $userToken = $_SESSION['user_token'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;
    $logDate = $_POST['log_date'] ?? '';
    $symptom = $_POST['symptom'] ?? '';
    $severity = $_POST['severity'] ?? 0;
    $notes = $_POST['notes'] ?? '';

    // Ensure user token and ID are available before proceeding.
    if (!$userToken || !$userId) {
        // Redirect with an error status if session data is missing.
        header('Location: '. session_url('health-tracker.php?status=error'));
        exit();
    }

    // Prepare the data for insertion into the 'health_logs' table.
    $insertData = [
        'user_id' => $userId,
        'log_date' => $logDate,
        'symptom' => $symptom,
        'severity' => $severity,
        'notes' => $notes
    ];

    // Call the Supabase query function to insert the new health log.
    $response = supabaseQuery('health_logs', 'POST', $insertData, [], $userToken);

    // Redirect the user back to the health tracker page with a status message.
    if (isset($response[0]['id'])) {
        // Success if the response contains the new entry's ID.
        header('Location: ' . session_url('health-tracker.php?status=success'));
    } else {
        // Error if the insertion failed.
        header('Location: ' . session_url('health-tracker.php?status=error'));
    }
    exit();
}
