<?php

$servername = "localhost";   // usually "localhost"
$username   = "root";        // default in XAMPP
$password   = "";            // default is empty
$database   = "muskan";    // your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
// echo "✅ Database connected successfully!";
?>
<?php include 'index.html'; ?>