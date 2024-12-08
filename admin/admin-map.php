<?php $pageTitle = "Map"; ?>
<?php require_once("./includes/Header.php"); ?>
<?php use app\src\save_location; ?>


<!-- Include Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<!-- Include Leaflet Control Geocoder CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<!-- Include Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<!-- Back Button -->
<a class="text-sky-500 hover:text-sky-600 focus:text-sky-600 dark:text-sky-600 dark:hover:text-sky-700" href="/admin/add-property">
    <i class="fr fi-rr-arrow-small-left"></i>
    Go back
</a>

<!-- Page Wrapper -->
<div class="page-wrapper">
    <!-- Map Container -->
    <div class="bg-slate-100 py-8 px-4 w-full rounded-xl lg:px-12 dark:bg-slate-800">
        <div class="map-container">
            <div id="map" class="map" style="width: 100%; height: 500px;"></div>
        </div>
    </div>

    <!-- Form for Submitting Data -->
<form id="locationForm" action="/admin/add-property.php" method="GET" class="mt-4">
    <label class="block mb-2" for="location_name">Location Name:</label>
    <input class="input w-full p-2 rounded-md border border-gray-300" type="text" id="location_name" name="location" required placeholder="Location Name">

    <!-- Hidden fields for latitude and longitude -->
    <input type="hidden" id="latitude" name="latitude">
    <input type="hidden" id="longitude" name="longitude">

    <!-- Submit Button -->
    <button class="bg-sky-500 hover:bg-sky-600 focus:bg-sky-600 text-white py-2 px-4 rounded-md mt-4" type="submit">
        Save and Return
    </button>
</form>

</div>

<?php require_once("./includes/Footer.php"); ?>

<!-- Map Initialization Script -->
<script>
    // Initialize the map and set default view
    const map = L.map('map').setView([7.103035, 124.835257], 18); // Default coordinates and zoom level

    // Add OpenStreetMap tiles
    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Add geocoder control to the map
    const geocoder = L.Control.geocoder().addTo(map);

    // Create a global variable to store the marker on the map
    let currentMarker = null;

    // Get the location input field
    const locationInput = document.getElementById('location_name');

    // Synchronize location input with geocoder's search functionality
    locationInput.addEventListener('input', function () {
        const searchQuery = locationInput.value;

        // Automatically trigger geocoder search
        geocoder.options.geocoder.geocode(searchQuery, function (results) {
            if (results && results.length > 0) {
                const result = results[0]; // Take the first result
                const lat = result.center.lat;
                const lon = result.center.lng;

                // Update the map view to the search result
                map.setView([lat, lon], 18);

                // If a marker exists, remove it
                if (currentMarker) {
                    map.removeLayer(currentMarker);
                }

                // Add a new marker to the map at the found location
                currentMarker = L.marker([lat, lon]).addTo(map).bindPopup(searchQuery).openPopup();

                // Update hidden latitude and longitude fields
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lon;

                // Update the location name input field with the selected location's name
                document.getElementById('location_name').value = searchQuery;
            }
        });
    });
</script>
