<?php
include('../db.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
$company_id = isset($_GET['company_id']) ? intval($_GET['company_id']) : null;
$start_time = isset($_GET['start_time']) ? $_GET['start_time'] : null;
$end_time = isset($_GET['end_time']) ? $_GET['start_time'] : null;
$fees = isset($_GET['fees']) ? intval($_GET['fees']) : null;
$query = "SELECT * FROM flights WHERE is_completed =0";
if ($company_id) {
    $query .= " AND company_id = $company_id";
}
if ($start_time) {
    $query .= " AND start_time = $start_time";
}if ($end_time) {
    $query .= " AND start_time = $end_time";
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