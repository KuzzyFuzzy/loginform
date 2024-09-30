<?php
session_start();
include 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

// Get search query
$search = isset($_GET['query']) ? $_GET['query'] : '';
$search_query = '%' . $search . '%';

// Prepare SQL query to fetch videos with search filter
$sql = "SELECT * FROM videos WHERE title LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $search_query);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die(json_encode([]));
}

$videos = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();

echo json_encode($videos);
?>
