<?php
// Allow the frontend (http://localhost:8000) to access this API with credentials
header("Access-Control-Allow-Origin: http://localhost:8000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle OPTIONS request for preflight CORS check
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include database connection
require_once 'C:/wamp64/www/wep-project/backend/db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'User not logged in.']);
    exit;
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Handle GET request (fetch user profile)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch user details
    $query = "SELECT * FROM passengers WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Database error.']);
        exit;
    }
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If no user found
    if ($result->num_rows === 0) {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'User not found.']);
        exit;
    }

    // Return user data as JSON
    $user = $result->fetch_assoc();
    header('Content-Type: application/json');
    echo json_encode($user);
    exit;
}

// Handle POST request (update user profile)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from POST request
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $profile_picture = $_FILES['profile_picture'] ?? null;
    $passport_image = $_FILES['passport_image'] ?? null;

    // Validate input
    if (empty($name) || empty($email) || empty($phone)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'All fields are required.']);
        exit;
    }

    // Handle profile picture upload
    $profile_pic_path = null;
    if ($profile_picture && $profile_picture['error'] === UPLOAD_ERR_OK) {
        $profile_pic_path = 'uploads/' . basename($profile_picture['name']);
        if (!move_uploaded_file($profile_picture['tmp_name'], 'C:/wamp64/www/wep-project/' . $profile_pic_path)) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to upload profile picture.']);
            exit;
        }
    }

    // Handle passport image upload
    $passport_pic_path = null;
    if ($passport_image && $passport_image['error'] === UPLOAD_ERR_OK) {
        $passport_pic_path = 'uploads/' . basename($passport_image['name']);
        if (!move_uploaded_file($passport_image['tmp_name'], 'C:/wamp64/www/wep-project/' . $passport_pic_path)) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to upload passport image.']);
            exit;
        }
    }

    // Update user details in database
    $query = "UPDATE passengers SET name = ?, email = ?, tel = ?, photo = ?, passport_img = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Database error.']);
        exit;
    }
    $stmt->bind_param('sssssi', $name, $email, $phone, $profile_pic_path, $passport_pic_path, $user_id);
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Profile updated successfully.']);

    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to update profile.']);
    }
    exit;
}
?>