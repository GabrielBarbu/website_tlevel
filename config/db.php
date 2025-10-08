<?php
/**
 * Supabase Database and Authentication Interface
 *
 * This file provides a set of functions to interact with the Supabase API.
 * It handles database queries (CRUD operations) and user authentication (sign-up, sign-in, sign-out).
 * It relies on environment variables for Supabase credentials.
 */
require_once __DIR__ . '/load_env.php';

// Load Supabase credentials from environment variables.
$supabaseUrl = getenv('SUPABASE_URL');
$supabaseKey = getenv('SUPABASE_KEY');

/**
 * Sends a query to a Supabase table via the REST API.
 *
 * @param string $table The name of the table to query.
 * @param string $method The HTTP method ('GET', 'POST', etc.).
 * @param array $data The data to send with a POST/PUT/PATCH request.
 * @param array $params The query parameters for a GET request (e.g., 'select', 'order').
 * @param string|null $bearerToken The user's JWT for authenticated requests.
 * @return array|null The decoded JSON response from the API, or an error array.
 */
function supabaseQuery($table, $method = 'GET', $data = [], $params = [], $bearerToken = null) {
    global $supabaseUrl, $supabaseKey;

    // Construct the base URL for the API request.
    $url = "$supabaseUrl/rest/v1/$table";

    // Append query parameters if provided.
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    // Set up the necessary headers for the Supabase API.
    $headers = [
        'apikey: ' . $supabaseKey,
        'Content-Type: application/json',
        'Prefer: return=representation' // Ensures the response contains the inserted/updated data.
    ];

    // Use the user's JWT for authorization if available, otherwise use the public API key.
    if ($bearerToken) {
        $headers[] = 'Authorization: Bearer ' . $bearerToken;
    } else {
        $headers[] = 'Authorization: Bearer ' . $supabaseKey;
    }

    // Initialize cURL session.
    $ch = curl_init();

    // Set cURL options.
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Use 'true' in production with a valid SSL certificate.
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Use '2' in production.
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    // Handle POST requests.
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    // Execute the cURL request.
    $response = curl_exec($ch);

    // Check for cURL errors.
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return ['curl_error' => $error_msg];
    }

    // Close the cURL session.
    curl_close($ch);

    // Decode and return the JSON response.
    return json_decode($response, true);
}

/**
 * Signs up a new user using Supabase Auth.
 *
 * @param string $email The user's email address.
 * @param string $password The user's chosen password.
 * @param string $username The user's chosen username.
 * @return array The decoded JSON response from the Auth API.
 */
function supabaseAuthSignUp($email, $password, $username) {
    global $supabaseUrl, $supabaseKey;
    $url = "$supabaseUrl/auth/v1/signup";

    // Prepare the data payload for the sign-up request.
    $data = [
        'email' => $email,
        'password' => $password,
        'data' => ['username' => $username] // Additional user metadata.
    ];

    $headers = ['apikey: ' . $supabaseKey, 'Content-Type: application/json'];

    // Perform the API call to the sign-up endpoint.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

/**
 * Logs in a user using Supabase Auth with email and password.
 *
 * @param string $email The user's email address.
 * @param string $password The user's password.
 * @return array The decoded JSON response from the Auth API, containing access tokens.
 */
function supabaseAuthSignIn($email, $password) {
    global $supabaseUrl, $supabaseKey;
    $url = "$supabaseUrl/auth/v1/token?grant_type=password";

    $data = ['email' => $email, 'password' => $password];

    $headers = ['apikey: ' . $supabaseKey, 'Content-Type: application/json'];

    // Perform the API call to the token endpoint.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

/**
 * Logs out a user by clearing their session data.
 * Note: This is a client-side logout. For full stateless logout,
 * the JWT should be invalidated on the server if using refresh tokens.
 */
function supabaseAuthSignOut() {
    unset($_SESSION['user_token']);
    unset($_SESSION['user_id']);
}

/**
 * Checks if a user is currently logged in by verifying session data.
 *
 * @return bool True if the user is logged in, false otherwise.
 */
function isLoggedIn() {
    return isset($_SESSION['user_token']);
}
?>
