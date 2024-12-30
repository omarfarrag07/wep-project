<?php
include('../db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $flight_id = isset($_POST['flight_id']) ? intval($_POST['flight_id']) : null;
    $passenger_id = isset($_POST['passenger_id']) ? intval($_POST['passenger_id']) : null;
    $cash = isset($_POST['cash']) ? intval($_POST['cash']) : null;

if (!$flight_id || !$passenger_id||$cash===null) {
    http_response_code(400);
    echo "Passenger and flight ID are required.";
    exit;
}


    $stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
     $stmt->bind_param("i", $flight_id);
     $stmt->execute();
     $result_flight = $stmt->get_result();
 
     $stmt = $conn->prepare("SELECT * FROM passengers WHERE id = ?");
     $stmt->bind_param("i", $passenger_id);
     $stmt->execute();
     $result_passenger = $stmt->get_result();
    if ($result_flight->num_rows == 0 || $result_passenger->num_rows == 0) {
        http_response_code(405);
        echo"Passenger or flight ID incorrect";
        exit;
    }
    $flight = $result_flight->fetch_assoc();
    $passenger = $result_passenger->fetch_assoc();
    if ($cash ===0){
        $balance=$passenger['account_balance'];
        $fees=$flight['fees'];
        if($balance<$fees){
        http_response_code(500);
        exit;
    }
        
    }
    $stmt = $conn->prepare("SELECT * FROM flight_passenger WHERE flight_id =$flight_id AND passenger_id=$passenger_id ");
    $stmt->execute();
    $resualt=$stmt->get_result();
    if($resualt->num_rows > 0){
        http_response_code(205);
        echo "Passenger is already registered in the flight";
        exit;
    }
    $stmt = $conn->prepare("INSERT INTO flight_passenger (flight_id, passenger_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $flight_id, $passenger_id);
    if ($stmt->execute()) {
        http_response_code(205);
        echo "Passenger successfully added to the flight.";
    } else {
        http_response_code(400);
        echo "Error: Could not insert passenger to the flight.";
    }
    
}

?>