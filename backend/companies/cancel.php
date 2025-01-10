<?php
require '../db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true); 

if (isset($data['id']) && is_numeric($data['id'])) {
    $flight_id = (int)$data['id'];

    $conn->begin_transaction();

    try {
        $flightQuery = "SELECT fees, company_id FROM flights WHERE id = ?";
        $stmt = $conn->prepare($flightQuery);
        $stmt->bind_param('i', $flight_id);
        $stmt->execute();
        $flightResult = $stmt->get_result();

        if ($flightResult->num_rows > 0) {
            $flight = $flightResult->fetch_assoc();
            $flightFees = $flight['fees'];
            $companyId = $flight['company_id'];

            $passengerQuery = "SELECT passenger_id FROM flight_passenger WHERE flight_id = ? AND status = 'registered'";
            $stmt = $conn->prepare($passengerQuery);
            $stmt->bind_param('i', $flight_id);
            $stmt->execute();
            $passengerResult = $stmt->get_result();

            $returnFeesQuery = "UPDATE passengers SET account_balance = account_balance + ? WHERE id = ?";
            $returnStmt = $conn->prepare($returnFeesQuery);

            $passengerCount = 0;

            while ($passenger = $passengerResult->fetch_assoc()) {
                $passengerId = $passenger['passenger_id'];
                $returnStmt->bind_param('di', $flightFees, $passengerId);
                $returnStmt->execute();
                $passengerCount++;
            }

            $deletePassengerQuery = "DELETE FROM flight_passenger WHERE flight_id = ?";
            $deleteFlightQuery = "DELETE FROM flights WHERE id = ?";
            $stmt = $conn->prepare($deletePassengerQuery);
            $stmt->bind_param('i', $flight_id);
            $stmt->execute();

            $stmt = $conn->prepare($deleteFlightQuery);
            $stmt->bind_param('i', $flight_id);
            $stmt->execute();
            $totalRefund = $passengerCount * $flightFees;
            if ($passengerCount > 0) {
                $totalRefund = $passengerCount * $flightFees;
                $updateCompanyQuery = "UPDATE companies SET account_balance = account_balance - ? WHERE id = ?";
                $stmt = $conn->prepare($updateCompanyQuery);
                $stmt->bind_param('di', $totalRefund, $companyId);
                $stmt->execute();
            }
            

            $conn->commit();

            echo json_encode(["success" => true, "message" => "Flight successfully canceled, and fees refunded to passengers."]);
        } else {
            throw new Exception("Flight not found.");
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No flight ID provided."]);
}

$conn->close();
?>
