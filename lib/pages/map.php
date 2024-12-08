<?php

use app\assets\DB;

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "Map";
require_once("./includes/Header.php");

?>

<!-- Include Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<!-- Include Leaflet Control Geocoder CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<!-- Include Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<?php
// Retrieve GET parameters
$propertyID = isset($_GET['propertyID']) ? $_GET['propertyID'] : null;


// Validate the presence of required parameters
if (is_empty($propertyID)) {
    displayMessage("Invalid property details provided.", "text-rose-500");
    header("Refresh: 2, /404", true, 301);
    exit();
}

// Initialize DB connection
$con = DB::getInstance();

// Fetch property's latitude and longitude from the database
$getProperty = $con->select(
    "latitude, longitude, title",
    "properties",
    "WHERE id = ? AND status = 'available'",
    ...[$propertyID]
);

// Check if the property exists
if ($getProperty->num_rows < 1) {
    displayMessage("Property not found.", "text-rose-500");
    header("Refresh: 2, /404", true, 301);
    exit();
}

$property = $getProperty->fetch_object();

// Ensure latitude and longitude are available
if (is_empty($property->latitude) || is_empty($property->longitude)) {
    displayMessage("Location data is not available for this property.", "text-rose-500");
    header("Refresh: 2, /property-details.php?propertyID={$propertyID}", true, 301);
    exit();
}

$latitude = $property->latitude;
$longitude = $property->longitude;
$propertyTitle = addslashes($property->title); // Escape quotes for JavaScript

?>



<!-- Page Wrapper -->
<div class="page-wrapper">
    <!-- Map Container -->
    <div class="bg-slate-100 py-0 px-4 w-full rounded-none dark:bg-slate-800">
        <div class="map-container">
            <!-- Use vh units to make the map full height on all screen sizes -->
            <div id="map" class="map w-full h-[100vh]"></div>
        </div>
    </div>
</div>

<?php require_once("./includes/Footer.php"); ?>

<!-- Map Initialization Script -->
<script>
    // Property Coordinates
    const latitude = <?= json_encode($latitude) ?>;
    const longitude = <?= json_encode($longitude) ?>;
    const propertyTitle = <?= json_encode($propertyTitle) ?>;

    // Initialize the map centered on the property's coordinates
    const map = L.map('map').setView([latitude, longitude], 18);

    // Add OpenStreetMap tiles
    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Add a marker for the property's location
    L.marker([latitude, longitude]).addTo(map)
        .bindPopup("<b>" + propertyTitle + "</b>")
        .openPopup();

    // Optional: Add geocoder control if you want users to search for locations
    const geocoder = L.Control.geocoder().addTo(map);

    // Event listener for location selection via geocoder (optional)
    geocoder.on('markgeocode', function(e) {
        const lat = e.geocode.center.lat;
        const lon = e.geocode.center.lng;

        // Update the map view to the new location
        map.setView([lat, lon], 18);

        // Add or update the marker
        if (window.currentMarker) {
            map.removeLayer(window.currentMarker);
        }
        window.currentMarker = L.marker([lat, lon]).addTo(map)
            .bindPopup(e.geocode.name)
            .openPopup();
    });

    // Ensure map resizes when the window is resized
    window.addEventListener('resize', function() {
        map.invalidateSize();
    });
</script>
