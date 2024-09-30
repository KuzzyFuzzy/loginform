<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Summary</title>
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
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .print-btn, .attendance-btn, .dashboard-btn {
            background-color: #28a745;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
            text-align: center;
            margin: 0 auto;
            width: fit-content;
        }

        .buttons-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .buttons-container a {
            margin: 0 10px;
        }

        .total-counts {
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'database.php'; ?>

    <?php
    // Fetch attendance summary
    $sql = "SELECT name, last_name, attendance FROM students";
    $result = $conn->query($sql);

    // Initialize counters for absent and present students
    $totalAbsent = 0;
    $totalPresent = 0;

    // Count the number of absent and present students
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($row["attendance"] == "Absent") {
                $totalAbsent++;
            } else if($row["attendance"] == "Present") {
                $totalPresent++;
            }
        }
    }
    ?>

    <h1>Attendance Summary</h1>

    <table border="1">
        <tr>
            <th>Student Name</th>
            <th>Attendance Remarks</th>
        </tr>
        <?php
        // Reset the pointer of the result set
        $result->data_seek(0);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["name"] . " " . $row["last_name"] . "</td>";
                echo "<td>" . $row["attendance"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No attendance data found</td></tr>";
        }
        ?>
    </table>

    <p class="total-counts">Total Absent: <?php echo $totalAbsent; ?></p>
    <p class="total-counts">Total Present: <?php echo $totalPresent; ?></p>

    <div class="buttons-container">
        <a href="students.php" class="dashboard-btn">My Students</a>
        <a href="attendance.php" class="attendance-btn">Attendance Table</a>
        <button onclick="window.open('attendance_report.php', '_blank');">Generate Attendance Report</button>

    </div>
</body>
</html>
