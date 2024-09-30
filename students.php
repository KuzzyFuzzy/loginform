<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'my_databasekay');

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student data (make sure to check your table and field names)
$sql = "SELECT id, name, middle_name, last_name, birthday, photo FROM students";
$result = $conn->query($sql);

// Start outputting HTML
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .container {
            width: 80%;
            max-width: 1000px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #f06;
            color: white;
        }

        img {
            max-width: 100px;
            height: auto;
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .student-info {
            margin-bottom: 20px;
        }
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    color: #333;
    margin: 0;
    padding: 20px;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #ff6f61;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 12px;
    text-align: left;
}

th {
    background-color: #ff6f61;
    color: white;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

img {
    border-radius: 50%; /* Optional: Make images circular */
}

.button-container {
    margin-top: 20px;
    text-align: center;
}

button {
    background-color: #ff6f61;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 15px;
    cursor: pointer;
    font-size: 16px;
    margin: 0 10px;
}

button:hover {
    background-color: #e55b4a;
}

    </style>
</head>
<body>

<div class="container">
    <h1>Student List</h1>

    <?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'my_databasekay');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student data
$sql = "SELECT id, name, middle_name, last_name, birthday, photo FROM students";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    <link rel="stylesheet" href="style.css"> <!-- Add your CSS file -->
</head>
<body>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Middle Name</th>
        <th>Last Name</th>
        <th>Birthday</th>
        <th>Photo</th>
    </tr>
    
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['middle_name']); ?></td>
                <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['birthday']); ?></td>
                <td><img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="Photo" width="100"></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="7">No students found.</td>
        </tr>
    <?php endif; ?>
</table>

<!-- Buttons -->
<button onclick="location.href='dashboard.php'">Dashboard</button>
<button onclick="window.open('attendance_report.php', '_blank')">Print</button>

<?php
$conn->close();
?>

</body>
</html>
