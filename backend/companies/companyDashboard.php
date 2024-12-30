<?php
header('Content-Type: application/json');

require '../db.php'; 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    echo json_encode(['error' => 'Company not logged in']);
    exit;
}

$company_id = $_SESSION['user_id'];

$sql = "SELECT name, photo FROM companies WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['error' => 'Error preparing SQL statement']);
    exit;
}

$stmt->bind_param("i", $company_id);
$stmt->execute();

$result = $stmt->get_result();
$company_info = $result->fetch_assoc();

if (!$company_info) {
    echo json_encode(['error' => 'Company not found']);
    exit;
}

$sql_flights = "SELECT id, name, itinerary FROM flights WHERE company_id = ?";
$stmt_flights = $conn->prepare($sql_flights);
if ($stmt_flights === false) {
    echo json_encode(['error' => 'Error preparing flights SQL statement']);
    exit;
}

$stmt_flights->bind_param("i", $company_id);
$stmt_flights->execute();
$result_flights = $stmt_flights->get_result();

$flights = [];
while ($row = $result_flights->fetch_assoc()) {
    $flights[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'itinerary' => $row['itinerary'],
    ];
}

echo json_encode([
    'company_info' => $company_info,
    'flights' => $flights,
]);

$stmt->close();
$stmt_flights->close();
$conn->close();
?>
