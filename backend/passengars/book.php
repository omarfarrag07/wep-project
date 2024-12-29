<?php
include('../db.php');
function checkFlightAndPassengerExists($flight_id, $passenger_id) {
     $stmt = $conn->prepare("SELECT id FROM flights WHERE id = ?");
     $stmt->bind_param("i", $flight_id);
     $stmt->execute();
     $result_flight = $stmt->get_result();
 
     $stmt = $conn->prepare("SELECT id FROM passengers WHERE id = ?");
     $stmt->bind_param("i", $passenger_id);
     $stmt->execute();
     $result_passenger = $stmt->get_result();
    if ($result_flight->num_rows == 0 || $result_passenger->num_rows == 0) {
        echo"Passenger or flight ID incorrect";
        
    }

    return true;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $passenger_id = $_GET['passenger_id'];
    $flight_id = $_GET['flight_id'];
    $stmt = $conn->prepare("SELECT id FROM flights WHERE id = ?");
     $stmt->bind_param("i", $flight_id);
     $stmt->execute();
     $result_flight = $stmt->get_result();
 
     $stmt = $conn->prepare("SELECT id FROM passengers WHERE id = ?");
     $stmt->bind_param("i", $passenger_id);
     $stmt->execute();
     $result_passenger = $stmt->get_result();
    if ($result_flight->num_rows == 0 || $result_passenger->num_rows == 0) {
        echo"Passenger or flight ID incorrect";
        
        
    }
    
    $stmt = $conn->prepare("INSERT INTO flight_passenger (flight_id, passenger_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $flight_id, $passenger_id);
    if ($stmt->execute()) {
        echo "Passenger successfully added to the flight.";
    } else {
        echo "Error: Could not insert passenger to the flight.";
    }
    
    
}

?>