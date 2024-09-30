<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .attendance-column {
            width: 30%;
        }

        .submit-btn, .summary-btn, .dashboard-btn {
            background-color: #28a745;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .submit-btn:hover, .summary-btn:hover, .dashboard-btn:hover {
            background-color: #218838;
        }

        .buttons-container {
            text-align: center;
            margin-top: 20px;
        }

        .buttons-container a {
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <?php include 'database.php'; ?>

    <?php
    // Fetch all students
    $sql = "SELECT * FROM students"; 
    $result = $conn->query($sql); 

    // Handling form submission for attendance
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        foreach ($_POST['attendance'] as $id => $status) {
            // Update attendance in the database
            $sql = "UPDATE students SET attendance='$status' WHERE id=$id";
            $conn->query($sql); 
        }
    }
    ?>

    <h1>ITELEC3- WEBSYSTEMS AND TECHNOLOGIES ATTENDANCE</h1> 

    &nbsp;&nbsp;  <a href="attendance_summary.php" class="summary-btn">Attendance Summary</a>
<a href="students.php" class="dashboard-btn">My Students</a>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <table border="1">
            <tr>
                <th>ID No.</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th class="attendance-column">Attendance</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row["id"]."</td>";
                    echo "<td>".$row["name"]."</td>";
                    echo "<td>".$row["last_name"]."</td>";
                    echo "<td class='attendance-column'>";
                    echo "<select name='attendance[".$row["id"]."]'>";
                    echo "<option value='Present'>Present</option>";
                    echo "<option value='Absent'>Absent</option>";
                    echo "</select>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No records found</td></tr>";
            }
            ?>
        </table>
        <div class="buttons-container">
            
        
            

            <button type="submit" class="submit-btn">Submit Attendance</button>
        </div>
    </form>
</body>
</html>
