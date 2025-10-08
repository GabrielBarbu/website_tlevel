<?php
/**
 * The main entry point for the Health Advice Group website.
 * This file includes necessary configuration, starts the session, and displays
 * the main dashboard with links to the site's features.
 */

// Include the session management and database connection files.
require_once 'includes/session.php';
require_once 'config/db.php';

// --- Accessibility Settings ---
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Advice Group</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body class="<?= trim($body_classes) ?>">
    <?php include 'includes/header.php'; ?>

    <main class="container">
        <!-- Welcome card -->
        <div class="card">
            <h1>Welcome to the Health Advice Group</h1>
            <p style="text-align: center;">Your source for environmental health information and support.</p>
        </div>

        <!-- Dashboard grid for site features -->
        <div class="dashboard">
            <!-- Weather Forecast Card -->
            <div class="dashboard-card">
                <h2>Weather Forecast</h2>
                <p>Get the latest weather updates to plan your day.</p>
                <a href="<?= session_url('weather.php') ?>">Check Weather</a>
            </div>
            <!-- Air Quality Card -->
            <div class="dashboard-card">
                <h2>Air Quality</h2>
                <p>Monitor air quality in your area for better health.</p>
                <a href="<?= session_url('air-quality.php') ?>">Check Air Quality</a>
            </div>
            <!-- Health Advice Card -->
            <div class="dashboard-card">
                <h2>Personalised Health Advice</h2>
                <p>Receive health advice based on environmental conditions.</p>
                <a href="<?= session_url('health-advice.php') ?>">Get Advice</a>
            </div>
            <?php
            // Conditionally display the Health Tracker card only if the user is logged in.
            if (isLoggedIn()):
            ?>
            <div id="healthTrackerDashboard" class="dashboard-card">
                <h2>Personal Health Tracker</h2>
                <p>Track your health and symptoms over time.</p>
                <a href="<?= session_url('health-tracker.php') ?>">Track Health</a>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <?php
    // Include the site footer.
    include 'includes/footer.php';
    ?>
</body>
</html>
