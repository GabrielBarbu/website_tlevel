<?php
/**
 * This page displays the Personal Health Tracker.
 * It is a secure page that requires a user to be logged in.
 * It fetches and displays the user's past health logs and provides a form
 * to add new entries.
 */

// Include the session and database files.
require_once 'includes/session.php';
require_once 'config/db.php';

// --- Accessibility Settings ---
// This block reads the accessibility settings from the session and creates a string of CSS classes.
$body_classes = '';
if (isset($_SESSION['accessibility_settings'])) {
    if (!empty($_SESSION['accessibility_settings']['highContrast'])) {
        $body_classes .= ' high-contrast';
    }
    if (!empty($_SESSION['accessibility_settings']['largeFont'])) {
        $body_classes .= ' large-font';
    }
}
// --- End Accessibility Settings ---

// --- Security Check ---
// If the user is not logged in, redirect them to the login page.
if (!isLoggedIn()) {
    header('Location: ' . session_url('login.php'));
    exit();
}

// Get the logged-in user's ID and token from the session.
$userToken = $_SESSION['user_token'];
$healthLogs = [];

// --- Data Fetching ---
// Prepare the parameters to fetch all health logs for the user, ordered by date.
// The user_id filter is handled automatically by Supabase's Row-Level Security (RLS).
$params = [
    'select' => '*',
    'order' => 'log_date.desc'
];
// Fetch the logs from Supabase, providing the user's token for authorization.
$healthLogs = supabaseQuery('health_logs', 'GET', [], $params, $userToken);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Health Tracker - Health Advice Group</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body class="<?= trim($body_classes) ?>">
    <?php include 'includes/header.php'; ?>

    <main class="container">
        <!-- Form to add a new health entry -->
        <div class="card">
            <h1>Personal Health Tracker</h1>
            <p>Log your symptoms and track your health over time.</p>

            <!-- Form to add a new health entry -->
            <form action="<?= session_url('add_health_entry.php') ?>" method="post">
                <p class="required-note">* indicates a required field.</p>
                <label for="log_date">Date:<span class="required">*</span></label>
                <input type="date" id="log_date" name="log_date" value="<?= date('Y-m-d') ?>" required>

                <label for="symptom">Symptom:<span class="required">*</span></label>
                <input type="text" id="symptom" name="symptom" placeholder="e.g., Hay Fever, Asthma" required>

                <label for="severity">Severity (1-5):<span class="required">*</span></label>
                <input type="range" id="severity" name="severity" min="1" max="5" value="3" required>

                <label for="notes">Notes:</label>
                <textarea id="notes" name="notes" rows="4" placeholder="Any additional details..."></textarea>

                <button type="submit">Add Entry</button>
            </form>
        </div>

        <div class="card">
            <h2>Your Health Log</h2>
            <div class="health-logs-container">
                <?php
                // Check if any logs were returned and that there wasn't an error.
                if (!empty($healthLogs) && !isset($healthLogs['message'])):
                ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Symptom</th>
                                <th>Severity</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Loop through each log and display it in a table row.
                            foreach ($healthLogs as $log):
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($log['log_date']) ?></td>
                                    <td><?= htmlspecialchars($log['symptom']) ?></td>
                                    <td><?= htmlspecialchars($log['severity']) ?></td>
                                    <td><?= htmlspecialchars($log['notes']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <!-- Display a message if there are no logs or if an error occurred. -->
                    <p>You have no health entries yet.</p>
                    <?php // Display an error message if the Supabase query failed. ?>
                    <?php if(isset($healthLogs['message'])): ?>
                        <p class="error" role="alert">Supabase Error: <?= htmlspecialchars($healthLogs['message']) ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
