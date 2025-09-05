<?php
// reviews.php
header("Content-Type: application/json");

// Database config
$servername = "localhost";
$username   = "root"; 
$password   = "";
$dbname     = "muskan";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "DB connection failed: " . $conn->connect_error]);
    exit();
}

// POST = Add review
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name   = trim($_POST["name"] ?? "");
    $text   = trim($_POST["text"] ?? "");
    $rating = intval($_POST["rating"] ?? 0);

    if (empty($name) || empty($text) || $rating < 1 || $rating > 5) {
        echo json_encode(["success" => false, "error" => "Please fill all fields and give a valid rating."]);
        exit();
    }

    // Prepared statement for safety
    $stmt = $conn->prepare("INSERT INTO reviews (name, text, rating) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => $conn->error]);
        exit();
    }

    $stmt->bind_param("ssi", $name, $text, $rating);

    if ($stmt->execute()) {
        // Email notification
        $to = "13muskanahuja@gmail.com";  
        $subject = "📢 New Review Submitted";
        $message = "You received a new review:\n\n"
                 . "👤 Name: $name\n"
                 . "⭐ Rating: $rating\n"
                 . "💬 Review: $text\n\n";
        $headers = "From: noreply@yourdomain.com\r\n";

        @mail($to, $subject, $message, $headers);

        echo json_encode(["success" => true, "message" => "Review submitted successfully!"]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// GET = Fetch reviews
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $sql = "SELECT id, name, text, rating, created_at FROM reviews ORDER BY id DESC";
    $result = $conn->query($sql);

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    echo json_encode($reviews);
    $conn->close();
    exit();
}
?>
