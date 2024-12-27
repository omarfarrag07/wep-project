<?php
include('db.php');
$conn=new mysqli($host,$user,$pass,$db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
function checkFlightAndPassengerExists($flight_id, $passenger_id) {
    global $conn = new mysqli('localhost', 'root', 'your_password', 'flight_booking');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if flight and passenger exist
     $stmt = $conn->prepare("SELECT id FROM flights WHERE id = ?");
     $stmt->bind_param("i", $flight_id);
     $stmt->execute();
     $result_flight = $stmt->get_result();
 
     $stmt = $conn->prepare("SELECT id FROM passengers WHERE id = ?");
     $stmt->bind_param("i", $passenger_id);
     $stmt->execute();
     $result_passenger = $stmt->get_result();

    if ($result_flight->num_rows == 0 || $result_passenger->num_rows == 0) {
        $conn->close();
        return false;
    }

    $conn->close();
    return true;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
}

?>