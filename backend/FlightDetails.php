<?php
require 'db.php'; 


if (isset($_GET['id'])) {
    $flight_id = $_GET['id'];
    $query = "SELECT * FROM flights WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $flight_id);  
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
       
        $row = $result->fetch_assoc();
        
        echo json_encode($row);
    } else {
       
        echo json_encode(null);
    }

    $stmt->close();
} else {
    
    echo json_encode(null);
}

$conn->close();
?>
