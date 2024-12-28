
<?php

$host = 'localhost';
$user = 'root';
$pass = 'G4@67&*mQnY!';
$db = 'flight_booking';


global $conn;  
$conn = new mysqli($host, $user, $pass, $db); 



if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully to the database.";
}


?>