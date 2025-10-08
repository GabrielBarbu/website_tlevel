<?php
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

// Handle form submission to save settings to the session.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize the settings array in the session if it doesn't exist.
    if (!isset($_SESSION['accessibility_settings'])) {
        $_SESSION['accessibility_settings'] = [];
    }

    // Save the new settings from the form submission.
    // The `isset()` check returns true if the checkbox was checked, false otherwise.
    $_SESSION['accessibility_settings']['highContrast'] = isset($_POST['high-contrast']);
    $_SESSION['accessibility_settings']['largeFont'] = isset($_POST['large-font']);

    // Explicitly save the session data before redirecting.
    session_write_close();

    // Redirect back to the settings page with a success message.
    header('Location: ' . session_url('settings.php?saved=true'));
    exit();
}

// Load the current settings from the session to populate the form.
$currentSettings = $_SESSION['accessibility_settings'] ?? [];
$isHighContrast = $currentSettings['highContrast'] ?? false;
$isLargeFont = $currentSettings['largeFont'] ?? false;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Health Advice Group</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body class="<?= trim($body_classes) ?>">
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="card">
            <h1>Accessibility Settings</h1>
            <p>Use the options below to adjust the site to your needs.</p>
            
            <?php if (isset($_GET['saved'])): ?>
                <p class="success">Your settings have been saved.</p>
            <?php endif; ?>

            <form id="settings-form" method="POST" action="<?= session_url('settings.php') ?>">
                <div class="setting-item">
                    <label for="high-contrast">High Contrast Mode:</label>
                    <input type="checkbox" id="high-contrast" name="high-contrast" <?= $isHighContrast ? 'checked' : '' ?>>
                </div>
                <div class="setting-item">
                    <label for="large-font">Large Font Size:</label>
                    <input type="checkbox" id="large-font" name="large-font" <?= $isLargeFont ? 'checked' : '' ?>>
                </div>
                <button type="submit">Save Settings</button>
            </form>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
