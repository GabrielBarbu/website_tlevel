<?php
/**
 * This page displays the air quality data.
 * It allows a user to enter a location, finds its coordinates, and then
 * fetches the current air quality index from the OpenWeatherMap API.
 */

// Include necessary files.
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
    <title>Air Quality - Health Advice Group</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body class="<?= trim($body_classes) ?>">
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="card">
            <h1>Air Quality Monitoring</h1>
            <p>Enter a location to get the current air quality data.</p>

            <form method="POST" action="<?= session_url('air-quality.php') ?>">
                <p class="required-note">* indicates a required field.</p>
                <label for="location">Location:<span class="required">*</span></label>
                <input type="text" id="location" name="location" required>
                <button type="submit">Get Air Quality</button>
            </form>

            <div id="air-quality-results">
                <?php
                // This block is inside the HTML body because it needs to output directly to the page.
                require_once __DIR__ . '/config/load_env.php';
                $airQualityResult = null;
                $apiKey = getenv('OPENWEATHERMAP_API_KEY');

                // --- API Key Check ---
                if (!$apiKey || $apiKey === 'YOUR_OPENWEATHERMAP_API_KEY') {
                    $airQualityResult = "OpenWeatherMap API key is not configured. Please set it in the .env file.";
                } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // --- Form Processing ---
                    $location = $_POST['location'];
                    $lat = null;
                    $lon = null;

                    // Check if the user entered coordinates.
                    if (preg_match('/^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/', $location, $matches)) {
                        $lat = $matches[1];
                        $lon = $matches[3];
                    } else {
                        // If it's a city name, use the Geocoding API to get coordinates.
                        $geoApiUrl = "http://api.openweathermap.org/geo/1.0/direct?q=".urlencode($location)."&limit=1&appid={$apiKey}";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $geoApiUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $geoResponse = curl_exec($ch);
                        curl_close($ch);
                        $geoData = json_decode($geoResponse, true);

                        if ($geoData && !empty($geoData) && isset($geoData[0]['lat'])) {
                            $lat = $geoData[0]['lat'];
                            $lon = $geoData[0]['lon'];
                        }
                    }

                    // If we have valid coordinates, fetch the air quality data.
                    if ($lat && $lon) {
                        $airApiUrl = "http://api.openweathermap.org/data/2.5/air_pollution?lat={$lat}&lon={$lon}&appid={$apiKey}";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $airApiUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $airResponse = curl_exec($ch);
                        curl_close($ch);
                        $airData = json_decode($airResponse, true);

                        // If the API call was successful, store the main results.
                        if ($airData && isset($airData['list'][0])) {
                            $airQualityResult = $airData['list'][0];
                        } else {
                            $airQualityResult = "Could not retrieve air quality data for the provided coordinates.";
                        }
                    } else {
                        $airQualityResult = "Could not find coordinates for the specified location.";
                    }
                }

                /**
                 * Converts the Air Quality Index (AQI) number to a human-readable text.
                 * @param int $aqi The AQI value (1-5).
                 * @return string The textual representation (e.g., 'Good', 'Poor').
                 */
                function getAqiText($aqi) {
                    switch ($aqi) {
                        case 1: return 'Good';
                        case 2: return 'Fair';
                        case 3: return 'Moderate';
                        case 4: return 'Poor';
                        case 5: return 'Very Poor';
                        default: return 'Unknown';
                    }
                }

                // --- Display Results ---
                if (is_string($airQualityResult)):
                ?>
                    <p class="error" role="alert"><?php echo $airQualityResult; ?></p>
                <?php elseif ($airQualityResult && is_array($airQualityResult)): ?>
                    <h2>Air Quality in <?= htmlspecialchars($_POST['location']) ?></h2>
                    <p><strong>Overall:</strong> <?php echo getAqiText($airQualityResult['main']['aqi']); ?></p>
                    <p><strong>CO:</strong> <?php echo $airQualityResult['components']['co']; ?> μg/m³</p>
                    <p><strong>NO:</strong> <?php echo $airQualityResult['components']['no']; ?> μg/m³</p>
                    <p><strong>NO2:</strong> <?php echo $airQualityResult['components']['no2']; ?> μg/m³</p>
                    <p><strong>O3:</strong> <?php echo $airQualityResult['components']['o3']; ?> μg/m³</p>
                    <p><strong>SO2:</strong> <?php echo $airQualityResult['components']['so2']; ?> μg/m³</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
