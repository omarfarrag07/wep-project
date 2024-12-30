<?php
header("Access-Control-Allow-Origin: http://localhost:8000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized. Please log in as a company.']);
    exit;
}

$company_id = $_SESSION['user_id'];
$name = $_POST['name'] ?? null;
$bio = $_POST['bio'] ?? null;
$address = $_POST['address'] ?? null;
$tel = $_POST['tel'] ?? null;
$email = $_POST['email'] ?? null;
$location = $_POST['location'] ?? null;
$password = $_POST['password'] ?? null;

if (!$name || !$bio || !$address || !$tel || !$email || !$location) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields except password are required.']);
    exit;
}

$photo = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../../uploads/';
    $file_name = basename($_FILES['photo']['name']);
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
        $photo = $file_name;
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload file.']);
        exit;
    }
}

$query = "UPDATE companies SET name = ?, bio = ?, address = ?, tel = ?, email = ?, location = ?";
$params = [$name, $bio, $address, $tel, $email, $location];
$types = "ssssss";

if ($photo) {
    $query .= ", photo = ?";
    $params[] = $photo;
    $types .= "s";
}

if ($password) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query .= ", password = ?";
    $params[] = $hashed_password;
    $types .= "s";
}

$query .= " WHERE id = ?";
$params[] = $company_id;
$types .= "i";

$stmt = $conn->prepare($query);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: Failed to prepare statement.']);
    exit;
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: Failed to update profile.']);
}
exit;
