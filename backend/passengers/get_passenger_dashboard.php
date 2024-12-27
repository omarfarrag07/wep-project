<?php
        require '../db.php';

        session_start();

        if(!isset($_SESSION['user_id']) || $_SESSION['user_type']!== 'passenger'){
            http_response_code(403);
            echo json_encode(['error' => 'unauthorized']);
            exit;
        }

        $passengerId = $_SESSION['user_id'];

        try {
            $currentQuery="SELECT F.id, f.name, f.itinerary, f.time, f.fees
                    FROM flights f
                    JOIN flight_passenger b on f.id = b.flight_id
                    WHERE b.passenger_id =? AND f.start_time > NOW()
            ";
            $stablish =$conn->prepare($currentQuery);
            $stablish->bind_param('i',$passengerId);
            $stablish->execute();
            $currentFlights =$stablish->get_result()->fetch_all(MYSQLI_ASSOC);

            $completedQuery="SELECT f.id, f.name, f.itinerary, f.time, f.fees 
                              FROM flights f
                              JOIN flight_passenger b ON f.id = b.flight_id
                              WHERE b.passenger_id = ? AND f.start_time <= NOW()";
             $stablish =$conn->prepare($completedQuery);
             $stablish->bind_param('i',$passengerId);
             $stablish->execute();
             $completedFlights=$stablish->get_result()->fetch_all(MYSQLI_ASSOC);
             
             echo json_encode([
                'current_flights' => $currentFlights,
                'completed_flights' => $completedFlights,
             ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server error.']);
        }

?>