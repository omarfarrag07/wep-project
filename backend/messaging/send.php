<?php
  header('Content-Type: application/json');
  require '../db.php';

  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    session_start();
    $company_id =$_SESSION['user_id'];



    $data = json_decode(file_get_contents('php://input'), true);
    $flight_id=$data['flight_id'];
    $content=$data['content'];

    // error_log("Received flight_id: $flight_id");


    if(!$flight_id || !$content){
      http_response_code(400);
      echo json_encode(['error' => 'All fields are required']);
      exit;
    }


    // AND status = 'registered'
    $query= "SELECT passenger_id from flight_passenger WHERE flight_id = ?  ";
    $stablish=$conn->prepare($query);
    $stablish->bind_param('i',$flight_id);
    $stablish->execute();
    $result=$stablish->get_result();
  
    if ($result->num_rows === 0) {
      http_response_code(404);
      echo json_encode(['error' => 'No passengers found for the selected flight.']);
      exit;
  }
    
    // error_log("Insert Query: INSERT INTO messages (flight_id, from_user_id, to_user_id, content) VALUES ($flight_id, $company_id, $passenger_id, '$content')");

    $queryInsert= "INSERT INTO messages (flight_id ,from_user_id , to_user_id,content) VALUES (?,?,?,?)";
    $stablishInsert=$conn->prepare($queryInsert);

    while ($row =$result->fetch_assoc()) {
      $passenger_id=$row['passenger_id'];
      $stablishInsert->bind_param('iiis',$flight_id,$company_id,$passenger_id,$content);
      $stablishInsert->execute();
    }

    echo json_encode(['success' =>'Message sent to all passengers']);
    
  }

  
  
  
  
  
  ?>