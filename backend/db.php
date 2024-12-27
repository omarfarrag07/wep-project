<?php
$host = 'localhost';
$db = 'flight_booking';
$user = 'root';
$pass = 'Callme02$';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

?>