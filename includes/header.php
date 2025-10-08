<?php
/**
 * This file defines the header for the website.
 * It includes the logo, site title, and the main navigation bar.
 * The navigation links change depending on whether the user is logged in or not.
 */

// Include necessary files. The session must be started to check the login state.
require_once 'includes/session.php';
require_once 'config/db.php';

// Get the current page's filename to apply the 'active' class for styling.
$page = basename($_SERVER['PHP_SELF']);
?>
<header>
    <div class="header-content">
        <!-- Logo and site title link to the homepage -->
        <a href="<?= session_url('index.php') ?>" class="logo-link">
            <img src="logo_no_bg.png" alt="Health Advice Group Logo" class="logo">
            <h1>Health Advice Group</h1>
        </a>
        <nav id="main-nav">
            <ul>
                <!-- Standard navigation links -->
                <li><a href="<?= session_url('index.php') ?>" class="<?= $page == 'index.php' ? 'active' : '' ?>">Home</a></li>
                <li><a href="<?= session_url('weather.php') ?>" class="<?= $page == 'weather.php' ? 'active' : '' ?>">Weather</a></li>
                <li><a href="<?= session_url('air-quality.php') ?>" class="<?= $page == 'air-quality.php' ? 'active' : '' ?>">Air Quality</a></li>
                <li><a href="<?= session_url('health-advice.php') ?>" class="<?= $page == 'health-advice.php' ? 'active' : '' ?>">Health Advice</a></li>
                
                <?php
                // Conditionally display links based on login status.
                if (isLoggedIn()):
                ?>
                    <!-- Links for logged-in users -->
                    <li><a href="<?= session_url('health-tracker.php') ?>" class="<?= $page == 'health-tracker.php' ? 'active' : '' ?>">Health Tracker</a></li>
                    <li><a href="<?= session_url('settings.php') ?>" class="<?= $page == 'settings.php' ? 'active' : '' ?>">Settings</a></li>
                    <li><a href="<?= session_url('logout.php') ?>">Logout</a></li>
                <?php else: ?>
                    <!-- Links for guests -->
                    <li><a href="<?= session_url('login.php') ?>" class="<?= $page == 'login.php' ? 'active' : '' ?>">Login</a></li>
                    <li><a href="<?= session_url('register.php') ?>" class="<?= $page == 'register.php' ? 'active' : '' ?>">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <button class="hamburger-menu" aria-label="Toggle menu" aria-expanded="false">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>
</header>
