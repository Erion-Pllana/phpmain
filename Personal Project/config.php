<?php
$host = "localhost";
$user = "root";
$pass = ""; // or your MySQL password if you set one
$dbname = "database"; // ✅ your actual database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>