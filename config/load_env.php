<?php
/**
 * Environment Variable Loader
 *
 * This script loads configuration variables from a .env file in the project root.
 * This allows for keeping sensitive information like API keys out of version control.
 */
if (file_exists(__DIR__ . '/../.env')) {
    // Read the .env file into an array of lines.
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignore comments.
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        // Split each line into a name and value.
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // If the value is wrapped in quotes (single or double), remove them.
        if (preg_match('/^(\'|")(.*)\1$/', $value, $matches)) {
            $value = $matches[2];
        }
        
        // Set the environment variable if it doesn't already exist.
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}