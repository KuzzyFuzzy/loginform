<?php
session_start();
include 'database.php';

// Handle search query
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = '%' . $search . '%';

// Prepare SQL query
$student_sql = "SELECT id, name, middle_name, last_name, email, birthday, status, photo FROM students WHERE name LIKE ? OR middle_name LIKE ? OR last_name LIKE ? OR email LIKE ?";
$stmt = $conn->prepare($student_sql);
$stmt->bind_param("ssss", $search_query, $search_query, $search_query, $search_query);
$stmt->execute();
$student_result = $stmt->get_result();

// Fetch results
$students = [];
while ($student = $student_result->fetch_assoc()) {
    $students[] = $student;
}

// Return results as JSON
header('Content-Type: application/json');
echo json_encode($students);

// Close connection
$stmt->close();
$conn->close();
?>
