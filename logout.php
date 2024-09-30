<?php
session_start();
include 'database.php';

// Ensure the database connection is open
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Set status to offline (0)
    $stmt = $conn->prepare("UPDATE users SET status = 0 WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Handle prepare statement error
        echo "Error preparing statement: " . $conn->error;
        exit();
    }

    // Destroy the session
    session_unset();
    session_destroy();
}

// Close the database connection
$conn->close();

// Redirect to login page
header("Location: login.php");
exit();
?>
