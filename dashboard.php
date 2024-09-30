<?php
session_start();
include 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You need to log in first.'); window.location.href = 'login.php';</script>";
    exit();
}

// Handle user logout
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("UPDATE users SET status = 0, session_id = NULL WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    session_destroy();
    header('Location: login.php');
    exit();
}

// Prepare SQL query to fetch user information
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Close connection
$conn->close();

// Define profile picture URL
$profileImage = !empty($user['photo']) && file_exists($user['photo'])
    ? htmlspecialchars($user['photo'])
    : 'uploads/profile_images/default.png';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('uploads/5.jfif') no-repeat center center fixed;
            background-size: cover;
        }

        .menu-btn, .logout-btn {
            font-size: 18px;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 50%;
            width: 50px;
            height: 50px;
        }

        .menu-btn {
            position: fixed;
            top: 10px;
            left: 10px;
            background-color: #FF1493;
            z-index: 3;
        }

        .logout-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #FF1493;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border: 2px solid #fff;
            z-index: 3;
        }

        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: #FF69B4;
            color: #fff;
            overflow-x: hidden;
            transition: 0.3s ease;
            padding-top: 20px;
            z-index: 2;
        }

        .sidebar .nav-title {
            padding: 0 60px;
            color: #fff;
            font-size: 24px;
            margin-bottom: 20px;
            border-bottom: 1px solid #fff;
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 18px;
            transition: background-color 0.3s ease;
            margin: 0;
        }

        .sidebar a:hover, .sidebar .active {
            background-color: #FF1493;
        }

        .content {
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s ease;
            height: calc(100vh - 40px);
            position: relative;
            z-index: 1;
            background-color: rgba(255, 255, 255, 0.8); /* Slightly transparent background for readability */
            border-radius: 10px;
        }

        h1 {
            margin: 0;
            color: #333;
            text-align: center;
        }

        .welcome-msg {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-top: 20px;
        }
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .dashboard-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 800px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .bar-graph {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            height: 300px;
            margin-top: 20px;
            position: relative;
        }

        .bar {
            width: 50px;
            background-color: #f90;
            border-radius: 5px;
            transition: all 0.5s ease-in-out;
            position: relative;
            cursor: pointer;
        }

        .bar:hover {
            background-color: #f06;
            transform: scale(1.1);
        }

        .bar:hover .percentage {
            opacity: 1;
            transform: translateY(-20px);
        }

        .percentage {
            position: absolute;
            bottom: 110%;
            left: 50%;
            transform: translateX(-50%) translateY(0);
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            pointer-events: none;
        }

        .x-axis {
            position: absolute;
            bottom: -30px;
            left: 0;
            width: 100%;
            text-align: center;
            color: #555;
            font-size: 16px;
        }

        .bar-label {
            margin-top: 10px;
            text-align: center;
        }

        /* Bar transition effect (appearing from bottom) */
        @keyframes growBar {
            0% { height: 0; }
            100% { height: var(--bar-height); }
        }

        .bar {
            height: 0;
            animation: growBar 1s ease-in-out forwards;
        }
          * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .dashboard-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 800px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .bar-graph {
            position: relative;
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            height: 300px;
            margin-top: 20px;
            border-left: 2px solid #ccc;
            border-bottom: 2px solid #ccc;
            padding-left: 30px;
            position: relative;
        }

        .bar {
            width: 50px;
            background-color: #f90;
            border-radius: 5px;
            transition: all 0.5s ease-in-out;
            position: relative;
            cursor: pointer;
        }

        .bar:hover {
            background-color: #f06;
            transform: scale(1.1);
        }

        .bar:hover .percentage {
            opacity: 1;
            transform: translateY(-20px);
        }

        .percentage {
            position: absolute;
            bottom: 110%;
            left: 50%;
            transform: translateX(-50%) translateY(0);
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            pointer-events: none;
        }

        .bar-label {
            margin-top: 10px;
            text-align: center;
        }

        /* Add horizontal grid lines */
        .y-lines {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            pointer-events: none;
        }

        .y-line {
            position: absolute;
            width: 100%;
            height: 1px;
            background-color: #ccc;
        }

        .y-line:nth-child(1) { bottom: 0; }
        .y-line:nth-child(2) { bottom: 25%; }
        .y-line:nth-child(3) { bottom: 50%; }
        .y-line:nth-child(4) { bottom: 75%; }
        .y-line:nth-child(5) { bottom: 100%; }

        /* Labels for percentage marks */
        .y-label {
            position: absolute;
            left: -25px;
            font-size: 14px;
            color: #555;
        }

        .y-label:nth-child(1) { bottom: 0; }
        .y-label:nth-child(2) { bottom: 25%; }
        .y-label:nth-child(3) { bottom: 50%; }
        .y-label:nth-child(4) { bottom: 75%; }
        .y-label:nth-child(5) { bottom: 100%; }

        /* Bar transition effect (appearing from bottom) */
        @keyframes growBar {
            0% { height: 0; }
            100% { height: var(--bar-height); }
        }

        .bar {
            height: 0;
            animation: growBar 1s ease-in-out forwards;
        }
        .bar-graph {
    margin-top: 20px;
}

.bar {
    position: relative;
    background: #f06; /* Default color for absent */
    height: var(--bar-height);
    width: 30%; /* Set a fixed width for all bars */
    margin: 10px 0;
    opacity: 0.5; /* Make bars semi-transparent when they have no height */
}

.bar:nth-child(1) { background: #f06; } /* Absent */
.bar:nth-child(2) { background: #0a0; } /* Present */
.bar:nth-child(3) { background: #ffa500; } /* Late */

.percentage {
    position: absolute;
    bottom: 100%; /* Adjust as necessary */
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
}

.bar-label {
    text-align: center;
    color: #fff;
}


    </style>
</head>
<body>
    <button class="menu-btn" onclick="toggleSidebar()">☰</button>
    <button class="logout-btn" onclick="confirmLogout()" style="background-image: url('<?php echo $profileImage; ?>');"></button>
    <div id="sidebar" class="sidebar">
        <div class="nav-title">Dashboard</div>
        <a href="dashboard.php" class="active">Welcome</a>
        <!-- Add other sidebar links here -->
        <a href="userlist.php">User</a>
        <a href="attendance.php">Attendance</a>
        <a href="video_gallery.php">Gallery</a>
        <a href="account_settings.php">Account Settings</a>
        <a href="students.php">Students</a>
    </div>
    </div>
        <div class="welcome-msg">Hello, <?php echo htmlspecialchars($user['name']); ?>!<br> Welcome to your dashboard.
  <h1>Attendance Report</h1>
<div class="bar-graph">
    <?php
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'my_databasekay');
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch attendance data
    $sql = "SELECT name, attendance FROM students";
    $result = $conn->query($sql);

    // Initialize attendance data
    $attendance_data = [
        'Present' => 0,
        'Absent' => 0,
        'Late' => 0
    ];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $attendance_data[$row['attendance']]++;
        }
    }

    // Calculate total students
    $total_students = array_sum($attendance_data);

    // Output bars
    echo "
    <div class='bar' style='--bar-height: " . ($total_students > 0 ? ($attendance_data['Absent'] / $total_students) * 100 : 0) . "%'>
        <div class='percentage'>" . ($attendance_data['Absent'] > 0 ? round(($attendance_data['Absent'] / $total_students) * 100, 2) : 0) . "%</div>
        <div class='bar-label'>Absent</div>
    </div>
    <div class='bar' style='--bar-height: " . ($total_students > 0 ? ($attendance_data['Present'] / $total_students) * 100 : 0) . "%'>
        <div class='percentage'>" . ($attendance_data['Present'] > 0 ? round(($attendance_data['Present'] / $total_students) * 100, 2) : 0) . "%</div>
        <div class='bar-label'>Present</div>
    </div>
    <div class='bar' style='--bar-height: " . ($total_students > 0 ? ($attendance_data['Late'] / $total_students) * 100 : 0) . "%'>
        <div class='percentage'>" . ($attendance_data['Late'] > 0 ? round(($attendance_data['Late'] / $total_students) * 100, 2) : 0) . "%</div>
        <div class='bar-label'>Late</div>
    </div>";

    $conn->close();
    ?>

    <!-- Grid lines and labels -->
    <div class="y-lines">
        <div class="y-line"></div>
        <div class="y-line"></div>
        <div class="y-line"></div>
        <div class="y-line"></div>
        <div class="y-line"></div>
    </div>
    <div class="y-labels">
        <div class="y-label">0%</div>
        <div class="y-label">25%</div>
        <div class="y-label">50%</div>
        <div class="y-label">75%</div>
        <div class="y-label">100%</div>
    </div>
</div>


    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var content = document.querySelector(".content");
            if (sidebar.style.left === "0px") {
                sidebar.style.left = "-250px";
                content.style.marginLeft = "0";
            } else {
                sidebar.style.left = "0";
                content.style.marginLeft = "250px";
            }
        }

        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "?logout=true";
            }
        }
    </script>
</body>
</html>
