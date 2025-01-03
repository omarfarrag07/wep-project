<?php
header("Access-Control-Allow-Origin: http://localhost:8000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, OPTIONS");
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

$query = "SELECT id, name, bio, address, tel, location, account_balance, photo FROM companies WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: Failed to prepare statement.']);
    exit;
}

$stmt->bind_param('i', $company_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Company not found.']);
    exit;
}

$company = $result->fetch_assoc();

$response = [
    'id' => $company['id'],
    'name' => $company['name'],
    'bio' => $company['bio'],
    'address' => $company['address'],
    'tel' => $company['tel'],
    'loc' => $company['location'],
    'account_balance' => $company['account_balance'],
    'photo' => $company['photo'],
];

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
