<?php
/**
 * Health Advice Page
 *
 * This page provides personalized health advice based on the user's location.
 * It fetches weather and air quality data from the OpenWeatherMap API,
 * determines the environmental conditions (e.g., hot, rainy, poor air quality),
 * and then queries the Supabase database for relevant health advice.
 */
require_once 'includes/session.php';
require_once 'config/db.php';
require_once __DIR__ . '/config/load_env.php';

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

$adviceResult = null;
$locationName = '';

/**
 * Converts Air Quality Index (AQI) value to a human-readable text.
 * @param int $aqi The AQI value (1-5).
 * @return string The corresponding text description.
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

// Load the OpenWeatherMap API key from environment variables.
$apiKey = getenv('OPENWEATHERMAP_API_KEY');

// Check if the API key is configured.
if (!$apiKey || $apiKey === 'YOUR_OPENWEATHERMAP_API_KEY') {
    $adviceResult = "OpenWeatherMap API key is not configured. Please set it in the .env file.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission.
    $location = $_POST['location'];
    $locationName = htmlspecialchars($_POST['location']);
    $weatherData = null;
    $lat = null;
    $lon = null;

    // Check if the input is coordinates (lat, lon).
    if (preg_match('/^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/', $location, $matches)) {
        $lat = $matches[1];
        $lon = $matches[3];
    } else {
        // If not coordinates, geocode the location name to get coordinates.
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

    // If coordinates are available, proceed to fetch data.
    if ($lat && $lon) {
        // 1. Get current weather data.
        $weatherApiUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $weatherApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $weatherResponse = curl_exec($ch);
        curl_close($ch);
        $weatherData = json_decode($weatherResponse, true);

        // 2. Get current air quality data.
        $airQualityData = null;
        if ($weatherData && $weatherData['cod'] === 200) {
            $airApiUrl = "http://api.openweathermap.org/data/2.5/air_pollution?lat={$lat}&lon={$lon}&appid={$apiKey}";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $airApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $airResponse = curl_exec($ch);
            curl_close($ch);
            $airQualityData = json_decode($airResponse, true);
        }

        // 3. Determine conditions and query for advice if both API calls were successful.
        if ($weatherData && $airQualityData) {
            // Determine weather condition based on temperature and precipitation.
            $weatherCondition = 'moderate'; // default
            if ($weatherData['main']['temp'] > 25) $weatherCondition = 'hot';
            if ($weatherData['main']['temp'] < 10) $weatherCondition = 'cold';
            if (isset($weatherData['rain'])) $weatherCondition = 'rainy';

            // Determine air quality condition from the AQI.
            $aqi = $airQualityData['list'][0]['main']['aqi'];
            $airQualityCondition = getAqiText($aqi);

            // 4. Query Supabase for advice matching either the weather or air quality condition.
            $params = [
                'select' => 'advice',
                'or' => "(and(condition_type.eq.weather,condition_value.eq.{$weatherCondition}),and(condition_type.eq.air_quality,condition_value.eq.{$airQualityCondition}))"
            ];
            
            $adviceResult = supabaseQuery('health_advice', 'GET', [], $params);

        } else {
            $adviceResult = "Could not retrieve weather or air quality data for the provided coordinates.";
        }
    } else {
        $adviceResult = "Could not find coordinates for the specified location.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Advice - Health Advice Group</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body class="<?= trim($body_classes) ?>">
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="card">
            <h1>Health Advice</h1>
            <p>Enter a location to get personalized health advice based on the current weather and air quality.</p>

            <form method="POST" action="<?= session_url('health-advice.php') ?>">
                <p class="required-note">* indicates a required field.</p>
                <label for="location">Location:<span class="required">*</span></label>
                <input type="text" id="location" name="location" required>
                <button type="submit">Get Health Advice</button>
            </form>

            <div id="health-advice-results">
                <?php // Display the results, error messages, or advice. ?>
                <?php if (is_string($adviceResult)): ?>
                    <p class="error" role="alert"><?php echo $adviceResult; ?></p>
                <?php elseif ($adviceResult && is_array($adviceResult)): ?>
                    <h2>Health Advice for <?php echo $locationName; ?></h2>
                    <?php if (empty($adviceResult)): ?>
                        <p>No specific advice for the current conditions.</p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($adviceResult as $advice): ?>
                                <li><?php echo htmlspecialchars($advice['advice']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
