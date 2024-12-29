<?php
session_start();
require '../db.php';

$response = array('company_name' => 'Unknown Company');

if (isset($_SESSION['user_id'])) {
    $company_id = $_SESSION['user_id'];

   
    $query = "SELECT name FROM companies WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $company = $result->fetch_assoc();
        $response['company_name'] = $company['name'];
    }
}

$conn->close();


echo json_encode($response);
?>
