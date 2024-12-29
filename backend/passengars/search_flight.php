<?php
include('../db.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
$company_id = isset($_GET['company_id']) ? intval($_GET['company_id']) : null;
$takeoff = isset($_GET['takeoff']) ? $_GET['takeoff'] : null;
$destination = isset($_GET['destination']) ? $_GET['destination'] : null;
$fees = isset($_GET['fees']) ? intval($_GET['fees']) : null;
$query = "SELECT * FROM flights WHERE is_completed =0";
if ($company_id) {
    $query .= " AND company_id = $company_id";
}
if ($takeoff) {
    $query .= " AND takeoff = $takeoff";
}if ($destination) {
    $query .= " AND destination = $destination";
}if($fees) {
    $query .= " AND fees = $fees";
}
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    echo "No flights found";
} else { 
    while ($row = $result->fetch_assoc()) {
        echo "Flight ID: " . $row['id'] . " - Name: " . $row['name'] . "<br>";
    }
}

return $result;
}
?>