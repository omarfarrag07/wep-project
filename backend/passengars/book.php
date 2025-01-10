<?php

header('Content-Type: application/json');
include('../db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Decode JSON data from request body
    $input = json_decode(file_get_contents('php://input'), true);

    // Extract variables
    $flight_id = isset($input['flight_id']) ? intval($input['flight_id']) : null;
    $cash = isset($input['cash']) ? intval($input['cash']) : null;
    session_start(); // Ensure session is started
    $passenger_id = $_SESSION['user_id'] ?? null;

    // Validate input
    if (!$flight_id || !$passenger_id || $cash === null) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Passenger and flight ID are required."]);
        exit;
    }

    // Check flight and passenger existence
    $stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
    $stmt->bind_param("i", $flight_id);
    $stmt->execute();
    $result_flight = $stmt->get_result();

    $stmt = $conn->prepare("SELECT * FROM passengers WHERE id = ?");
    $stmt->bind_param("i", $passenger_id);
    $stmt->execute();
    $result_passenger = $stmt->get_result();

    if ($result_flight->num_rows == 0 || $result_passenger->num_rows == 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Passenger or flight ID is incorrect."]);
        exit;
    }

    $flight = $result_flight->fetch_assoc();
    $passenger = $result_passenger->fetch_assoc();

    // Check payment method and balance
    if ($cash === 0) {
        $balance = $passenger['account_balance'];
        $fees = $flight['fees'];
        if ($balance < $fees) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Insufficient balance for booking."]);
            exit;
        }
    }

    // Check if the passenger is already registered for the flight
    $stmt = $conn->prepare("SELECT * FROM flight_passenger WHERE flight_id = ? AND passenger_id = ?");
    $stmt->bind_param("ii", $flight_id, $passenger_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Passenger is already registered for the flight."]);
        exit;
    }

    // Add passenger to the flight
    $stmt = $conn->prepare("INSERT INTO flight_passenger (flight_id, passenger_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $flight_id, $passenger_id);

    if ($stmt->execute()) {
        http_response_code(200);
        $stmt = $conn->prepare("UPDATE flights SET registered_passengers = registered_passengers + 1 WHERE id = ?");
        $stmt->bind_param("i", $flight_id);
        $stmt->execute();
        $new_balance = $balance - $flight['fees'];
        $stmt = $conn->prepare("UPDATE passengers SET account_balance = ? WHERE id = ?");
        $stmt->bind_param("di", $new_balance, $passenger_id);
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "Passenger successfully added to the flight."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error: Could not insert passenger to the flight."]);
    }
}
?>
