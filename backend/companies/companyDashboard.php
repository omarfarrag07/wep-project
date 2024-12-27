<?php
header('Content-Type: application/json');

require '../db.php';

$sql = "SELECT id, name, itinerary FROM flights";
$result = $conn->query($sql);

$flights = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $flights[] = $row;
    }
}

echo json_encode($flights);

$conn->close();
?>
