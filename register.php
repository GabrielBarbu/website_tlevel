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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Health Advice Group</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body class="<?= trim($body_classes) ?>">
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="card">
            <h2>Register</h2>
            <?php // Display an error message if the 'error' URL parameter is set. ?>
            <?php if (isset($_GET['error'])): ?>
                <p class="error" role="alert"><?= htmlspecialchars($_GET['error']) ?></p>
            <?php endif; ?>
            
            <!-- Registration form that submits to register_process.php -->
            <form action="<?= session_url('register_process.php') ?>" method="post">
                <p class="required-note">* indicates a required field.</p>
                <label for="username">Username:<span class="required">*</span></label>
                <input type="text" id="username" name="username" required>
                <label for="email">Email:<span class="required">*</span></label>
                <input type="email" id="email" name="email" required>
                <label for="password">Password:<span class="required">*</span></label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Register</button>
            </form>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
