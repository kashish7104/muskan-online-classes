<?php
header("Content-Type: application/json");
error_reporting(0); // Prevent warnings breaking JSON

$to = "13muskanahuja@gmail.com";
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "muskan";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = trim($_POST["name"] ?? '');
    $email   = trim($_POST["email"] ?? '');
    $phone   = trim($_POST["phone"] ?? '');
    $subject = trim($_POST["subject"] ?? '');
    $message = trim($_POST["message"] ?? '');

    if (empty($name) || empty($email) || empty($subject)) {
        echo json_encode(["success" => false, "error" => "Please fill in all required fields."]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO enrollments (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "SQL prepare failed"]);
        exit();
    }
    $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

    if ($stmt->execute()) {
        $mail_subject = "New Enrollment from $name";
        $mail_body = "You received a new enrollment:\n\n".
                     "Name: $name\n".
                     "Email: $email\n".
                     "Phone: $phone\n".
                     "Subject: $subject\n".
                     "Message: $message\n";
        $headers = "From: $email";
        @mail($to, $mail_subject, $mail_body, $headers);

        echo json_encode(["success" => true, "message" => "Enrollment submitted successfully!"]);
    } else {
        echo json_encode(["success" => false, "error" => "Database insert failed"]);
    }

    $stmt->close();
    $conn->close();
}
?>
