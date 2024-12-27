<?php
require 'db.php';

$query = "SELECT * FROM flights";
$result = $conn->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            
            echo "<div class='flight-item'>";
            echo "<img src='../images/plane.jpg'  class='flight-image' />";
            echo "<a href='Flightdetails.html?id=" . urlencode($row['id']) . "'>";
            echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
            echo "<p>" . htmlspecialchars($row['itinerary']) . "</p>";
            echo "</a>";
            echo "</div>";
        }
    } else {
        echo "<p>No flights available.</p>";
    }
} else {
    echo "<p>Error fetching flights: " . $conn->error . "</p>";
}

$conn->close();
?>
