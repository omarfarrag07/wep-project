<?php
    require 'db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $itinerary = $_POST['itinerary'];
        $max_passengers = $_POST['max_passengers'];
        $fees = $_POST['fees'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $company_id = $_POST['company_id']; 

        $query = "INSERT INTO flights (company_id, name, itinerary, max_passengers, fees, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issdsss", $company_id, $name, $itinerary, $max_passengers, $fees, $start_time, $end_time);

        if ($stmt->execute()) {
           
           header("Location: ../views/GetAllFlights.html?success=1");
        } else {
            
            echo "Error: " . $conn->error;
        }
        $stmt->close();
        $conn->close();
    }
?>
