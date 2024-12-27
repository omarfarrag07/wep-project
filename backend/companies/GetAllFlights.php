<?php
session_start();

//law hwa logged in + company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}
require '../db.php';
$company_id = $_SESSION['user_id'];

$query = "SELECT * FROM flights WHERE company_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $company_id);
$stmt->execute();
$result = $stmt->get_result();

//array of flights
$flights = [];
if ($result) {
  
    while ($row = $result->fetch_assoc()) {
        $flights[] = $row;
    }
} else {
   
    echo json_encode(['error' => 'Error fetching flights: ' . $conn->error]);
    exit;
}

$conn->close();

// hab3to ll frontend json
echo json_encode($flights);
?>
