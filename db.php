<?php
$host = 'localhost'; 
$dbname = 'cnrsystem'; 
$username = 'root'; 
$password = '';

$port= '3307'; //natruyd port setup

$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
