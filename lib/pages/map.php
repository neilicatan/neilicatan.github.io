<?php

use app\assets\DB;

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
// get property id sa url
$propertyID = isset($_GET['propertyID']) ? $_GET['propertyID'] : null;


// validate property id
if (is_empty($propertyID)) {
    displayMessage("Invalid property details provided.", "text-rose-500");
    header("Refresh: 2, /404", true, 301);
    exit();
}

// initialize db 
$con = DB::getInstance();

// fetch property details
$getProperty = $con->select(
    "latitude, longitude, title",
    "properties",
    "WHERE id = ? AND status = 'available'",
    ...[$propertyID]
);

// check if naa property
if ($getProperty->num_rows < 1) {
    displayMessage("Property not found.", "text-rose-500");
    header("Refresh: 2, /404", true, 301);
    exit();
}

$property = $getProperty->fetch_object();

// check if naa longitude ug latitude
if (is_empty($property->latitude) || is_empty($property->longitude)) {
    displayMessage("Location data is not available for this property.", "text-rose-500");
    header("Refresh: 2, /property-details.php?propertyID={$propertyID}", true, 301);
    exit();
}

$latitude = $property->latitude;
$longitude = $property->longitude;
$propertyTitle = addslashes($property->title); // Escape quotes for JavaScript

?>

<div class="page-wrapper">
    <div class="bg-slate-100 py-0 px-4 w-full rounded-none dark:bg-slate-800">
        <div class="map-container">
            <div id="map" class="map w-full h-[100vh]"></div>
        </div>
    </div>
</div>

<?php require_once("./includes/Footer.php"); ?>

<!-- map shit -->
<script>
    // property Coordinates
    const latitude = <?= json_encode($latitude) ?>;
    const longitude = <?= json_encode($longitude) ?>;
    const propertyTitle = <?= json_encode($propertyTitle) ?>;

    // initialize the map centered on the property's coordinates
    const map = L.map('map').setView([latitude, longitude], 18);

    // add OpenStreetMap tiles
    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // add marker for the property's location
    L.marker([latitude, longitude]).addTo(map)
        .bindPopup("<b>" + propertyTitle + "</b>")
        .openPopup();

    // geocoder control to search for locations
    const geocoder = L.Control.geocoder().addTo(map);

    // event listener for geocoder
    geocoder.on('markgeocode', function(e) {
        const lat = e.geocode.center.lat;
        const lon = e.geocode.center.lng;

        // update to view new location
        map.setView([lat, lon], 18);

        // add or update marker
        if (window.currentMarker) {
            map.removeLayer(window.currentMarker);
        }
        window.currentMarker = L.marker([lat, lon]).addTo(map)
            .bindPopup(e.geocode.name)
            .openPopup();
    });

    // resize map kung resize winodw
    window.addEventListener('resize', function() {
        map.invalidateSize();
    });
</script>
