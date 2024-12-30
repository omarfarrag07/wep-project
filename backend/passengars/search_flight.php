<?php
include('../db.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Capture GET parameters
    $company_id = isset($_GET['company_id']) ? intval($_GET['company_id']) : null;
    $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : null;
    $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : null;
    $fees = isset($_GET['fees']) ? intval($_GET['fees']) : null;
    
    // Base query
    $query = "SELECT * FROM flights WHERE is_completed = 0";
    
    if ($company_id) {
        $query .= " AND company_id = $company_id";
    }
    if ($start_time && $end_time) {
        // Assuming start_time and end_time are timestamps
        $query .= " AND start_time BETWEEN '$start_time' AND '$end_time'";
    } elseif ($start_time) {
        $query .= " AND start_time >= '$start_time'";
    } elseif ($end_time) {
        $query .= " AND start_time <= '$end_time'";
    }
    if ($fees) {
        $query .= " AND fees = $fees";
    }

   
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    
   
    $flights = [];
    
  
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $flights[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($flights);
    } else {
      
        echo json_encode(['message' => 'No flights found']);
    }
}
}
?>
