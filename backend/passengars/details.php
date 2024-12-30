<?php
include('../db.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Get flight_id from the query string
    $flight_id = isset($_GET['flight_id']) ? intval($_GET['flight_id']) : null;

    if ($flight_id === null) {
        echo "Flight ID is missing.";
        exit;
    }

    // Prepare the query
    $query = "SELECT * FROM flights WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    // Bind the flight_id parameter to the query
    $stmt->bind_param("i", $flight_id);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if the flight exists
    if ($result && $result->num_rows > 0) {
        // Fetch the flight details
        $flight = $result->fetch_assoc(); // Fetch one row as an associative array

        // Echo the flight details in plain text
        echo "Flight Details:\n";
        echo "Flight ID: " . $flight['id'] . "\n";
        echo "Flight Name: " . $flight['name'] . "\n";
        echo "Flight Takeoff: " . $flight['takeoff'] . "\n";
        echo "Flight Destination:" . $flight['destination'] . "\n";
        echo "Flight start time: " . $flight['start_time'] . "\n";
        echo "Flight end time:" . $flight['end_time'] . "\n";
        echo "Flight fees :" . $flight['fees'] . "\n";
    } else {
        echo "No flights found.";
    }

     return $result;

}
?>
