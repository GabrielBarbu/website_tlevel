<?php
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

$weatherResult = null;
// Load the OpenWeatherMap API key from environment variables.
$apiKey = getenv('OPENWEATHERMAP_API_KEY');

// Check if the API key is configured.
if (!$apiKey || $apiKey === 'YOUR_OPENWEATHERMAP_API_KEY') {
    $weatherResult = "OpenWeatherMap API key is not configured. Please set it in the .env file.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission.
    $location = $_POST['location'];
    $lat = null;
    $lon = null;

    // Check if the input is coordinates (lat, lon).
    if (preg_match('/^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/', $location, $matches)) {
        $lat = $matches[1];
        $lon = $matches[3];
    } else {
        // If not coordinates, use the Geocoding API to find coordinates for the location name.
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

    // If coordinates are found, fetch the weather data.
    if ($lat && $lon) {
        $apiUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        $weatherData = json_decode($response, true);

        // Check if the API call was successful.
        if ($weatherData && $weatherData['cod'] === 200) {
            $weatherResult = $weatherData;
        } else {
            $weatherResult = "Could not retrieve weather data for the provided coordinates.";
        }
    } else {
        $weatherResult = "Could not find coordinates for the specified location.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Forecast - Health Advice Group</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body class="<?= trim($body_classes) ?>">
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="card">
            <h1>Weather Forecast</h1>
            <p>Enter a location to get the current weather forecast.</p>

            <form method="POST" action="<?= session_url('weather.php') ?>">
                <p class="required-note">* indicates a required field.</p>
                <label for="location">Location:<span class="required">*</span></label>
                <input type="text" id="location" name="location" required>
                <button type="submit">Get Forecast</button>
            </form>

            <div id="weather-results">
                <?php // Display the results or an error message. ?>
                <?php if (is_string($weatherResult)): ?>
                    <p class="error" role="alert"><?php echo $weatherResult; ?></p>
                <?php elseif ($weatherResult && is_array($weatherResult)): ?>
                    <h2>Weather in <?php echo htmlspecialchars($weatherResult['name']); ?></h2>
                    <p><strong>Temperature:</strong> <?php echo $weatherResult['main']['temp']; ?> &deg;C</p>
                    <p><strong>Condition:</strong> <?php echo htmlspecialchars(ucfirst($weatherResult['weather'][0]['description'])); ?></p>
                    <p><strong>Humidity:</strong> <?php echo $weatherResult['main']['humidity']; ?>%</p>
                    <p><strong>Wind Speed:</strong> <?php echo $weatherResult['wind']['speed']; ?> m/s</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>
