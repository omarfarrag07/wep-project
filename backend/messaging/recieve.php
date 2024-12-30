<?php
    header('Content-Type: application/json');
    require '../db.php';
    session_start();
    $passenger_id=$_SESSION['user_id'];

    $query="SELECT m.content, f.name AS flight_name, m.sent_at
        FROM messages m 
        LEFT JOIN flights f ON m.flight_id=f.id
        WHERE m.to_user_id=?
        ORDER BY m.sent_at DESC
    ";
    $stablish=$conn->prepare($query);
    $stablish->bind_param('i' ,$passenger_id);
    $stablish->execute();
    $result=$stablish->get_result();

    $messages=[];
    while($row=$result->fetch_assoc()){
        $messages[]=$row;
    }

    echo json_encode($messages);

?>