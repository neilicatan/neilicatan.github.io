<?php

use app\src\Login;

?>
<?php $pageTitle = "Map"; ?>
<?php require_once("./includes/Header.php"); ?>

<!-- Include Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<!-- Include Leaflet Control Geocoder CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<!-- Wrapper to ensure proper page layout with footer outside form -->
<div class="page-wrapper">
    <main class="flex-1 grid place-items-center w-full min-h-screen-map py-8 px-4 dark:bg-slate-900 dark:text-slate-400 lg:px-[30%]">
        <div class="bg-slate-100 py-8 px-4 w-full rounded-xl lg:px-12 dark:bg-slate-800" method="POST">
            <div class="map-container">
                <div id="map" class="map" style="width: 100%; height: 500px;"></div>
                
            </div>
        </div>
    </main>
</div>
<?php require_once("./includes/Footer.php"); ?>
<script>
    // Initialize map
    const map = L.map('map').setView([7.103035, 124.835257], 18); // set center and zoom level

    // OSM tile layer
    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Geocoder control
    const geocoder = L.Control.geocoder().addTo(map);

    // Event listener for location selection
    geocoder.on('markgeocode', function(e) {
        // Get latitude and longitude from the selected location
        const lat = e.geocode.center.lat;
        const lon = e.geocode.center.lng;

        // Set latitude and longitude values in hidden input fields
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lon;

        // Set location name or description in the input field
        document.getElementById('location_name').value = e.geocode.name;
    });
</script>