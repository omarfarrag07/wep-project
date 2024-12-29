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

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'passenger') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized. Please log in as a passenger.']);
    exit;
}

$passenger_id = $_SESSION['user_id'];
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$tel = $_POST['tel'] ?? null;

if (!$name || !$email || !$tel) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required.']);
    exit;
}

$photo = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'C:/wamp64/www/wep-project/uploads/';
    $photo_name = basename($_FILES['photo']['name']);
    $photo_target_file = $upload_dir . $photo_name;

    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_target_file)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload profile photo.']);
        exit;
    }
    $photo = $photo_name;
}

$passport_img = null;
if (isset($_FILES['passport_img']) && $_FILES['passport_img']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'C:/wamp64/www/wep-project/uploads/';
    $passport_name = basename($_FILES['passport_img']['name']);
    $passport_target_file = $upload_dir . $passport_name;

    if (!move_uploaded_file($_FILES['passport_img']['tmp_name'], $passport_target_file)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload passport image.']);
        exit;
    }
    $passport_img = $passport_name;
}

$query = "UPDATE passengers SET name = ?, email = ?, tel = ?" .
    ($photo ? ", photo = ?" : "") .
    ($passport_img ? ", passport_img = ?" : "") .
    " WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: Failed to prepare statement.']);
    exit;
}

if ($photo && $passport_img) {
    $stmt->bind_param('sssssi', $name, $email, $tel, $photo, $passport_img, $passenger_id);
} elseif ($photo) {
    $stmt->bind_param('ssssi', $name, $email, $tel, $photo, $passenger_id);
} elseif ($passport_img) {
    $stmt->bind_param('ssssi', $name, $email, $tel, $passport_img, $passenger_id);
} else {
    $stmt->bind_param('sssi', $name, $email, $tel, $passenger_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: Failed to update profile.']);
}
exit;
