<?php
header('Content-Type: application/json');

require '../db.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    echo json_encode(['error' => 'Company not logged in']);
    exit;
}

$company_id = $_SESSION['user_id'];

error_log('Company ID: ' . $company_id);

$sql = "
    SELECT f.id , f.name, f.itinerary
    FROM flights f
    WHERE f.company_id = ?
";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['error' => 'Error preparing SQL statement']);
    exit;
}

$stmt->bind_param("i", $company_id);

$stmt->execute();

if ($stmt->errno) {
    echo json_encode(['error' => 'Error executing query: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();

$flights = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $flights[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'itinerary' => $row['itinerary'],
           
        ];
    }
} else {
    $flights = ['message' => 'No flights found for this company'];
}

echo json_encode($flights);

$stmt->close();
$conn->close();
?>
