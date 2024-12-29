<?php
header("Access-Control-Allow-Origin: http://localhost:8000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'C:/wamp64/www/wep-project/backend/db.php';
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

if (!$name || !$bio || !$address) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required.']);
    exit;
}

$photo = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'C:/wamp64/www/wep-project/uploads/';
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

$query = "UPDATE companies SET name = ?, bio = ?, address = ?" . ($photo ? ", photo = ?" : "") . " WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: Failed to prepare statement.']);
    exit;
}

if ($photo) {
    $stmt->bind_param('ssssi', $name, $bio, $address, $photo, $company_id);
} else {
    $stmt->bind_param('sssi', $name, $bio, $address, $company_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: Failed to update profile.']);
}
exit;
