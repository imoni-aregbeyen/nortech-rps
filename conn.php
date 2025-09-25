<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nortech_rps";

// Create connection
// Initial connection (no database specified)
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Create database
// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
  // echo "Database created successfully";
} else {
  die("Error creating database: " . $conn->error);
}

// Close initial connection
$conn->close();

// Connect again, this time specifying the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// Now $conn is connected to the created database
?>