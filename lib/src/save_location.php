<?php
session_start();

// Assume location is saved in the database correctly
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Example of database saving process (assume successful save)
    $stmt = $db->prepare("INSERT INTO properties (latitude, longitude) VALUES (?, ?)");
    $stmt->bind_param("sdd", $latitude, $longitude);
    $stmt->execute();

    // Redirect back to add-property page with location as a query parameter
    header("Location: /admin/add-property.php?location=" . urlencode($locationName));
    exit();
}
?>
