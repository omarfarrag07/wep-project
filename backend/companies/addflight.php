<?php
require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $itinerary = $_POST['itinerary'];
    $max_passengers = $_POST['max_passengers'];
    $fees = $_POST['fees'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $takeoff = $_POST['takeoff'];
    $destination = $_POST['destination'];
    
    if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'company') {
        $company_id = $_SESSION['user_id'];

        $query = "INSERT INTO flights (company_id, name, itinerary, max_passengers, pending_passengers, fees, start_time, end_time,takeoff,destination) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?)";
        $stmt = $conn->prepare($query);

       
        $pending_passengers = $max_passengers;

        $stmt->bind_param("issddsssss", $company_id, $name, $itinerary, $max_passengers, $pending_passengers, $fees, $start_time, $end_time,$takeoff,$destination);

        if ($stmt->execute()) {
            header("Location: ../../views/companies/GetAllFlights.html?success=1");
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Error: Unauthorized access.";
    }
    $conn->close();
}
?>
