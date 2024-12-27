<?php
session_start();
require '../db.php';


if (isset($_SESSION['company_id'])) {
    $company_id = $_SESSION['company_id'];

   
    $query = "SELECT * FROM companies WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $company = $result->fetch_assoc();
} else {
    
    header("Location: login.php");   //if not logged in
    exit();
}


$conn->close();
?>
