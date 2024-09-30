<?php
session_start();
include 'database.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Prepare and execute SQL query to fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();

// Determine user status
$status = $user['status'] == 1 ? 'Online' : 'Offline';
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            background-color: #FFC0CB; /* Pink background */
            color: #333;
        }

        .sidebar {
            width: 200px;
            background-color: #FF69B4; /* Light pink */
            color: #fff;
            padding: 20px;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            transition: 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center; /* Center items horizontally */
        }

        .sidebar .back-button {
            background: none;
            border: none;
            padding: 10px;
            margin-bottom: 5px;
            cursor: pointer;
        }

        .sidebar .back-button svg {
            width: 24px; /* Adjust size as needed */
            height: 24px;
        }

        .sidebar h2 {
            color: #fff;
            margin-top: 0;
            font-size: 24px;
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #FF1493; /* Darker pink on hover */
        }

        .sidebar .active {
            background-color: #FF1493; /* Darker pink for active link */
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            display: flex;
            flex-direction: column;
            align-items: center; /* Center the content horizontally */
            justify-content: center; /* Center the content vertically */
        }

        .profile-info {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            width: 100%;
            margin: 20px 0; /* Add margin to the top and bottom */
        }

        .profile-info img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .profile-info p {
            margin: 10px 0;
        }

        .logout-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            text-decoration: none;
            font-size: 16px;
            background-color: #d32f2f; /* Red background */
            color: white;
            text-align: center;
            border-radius: 5px;
        }

        .logout-button:hover {
            background-color: #b71c1c; /* Darker red on hover */
        }

        @media screen and (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .sidebar a {
                float: left;
                width: 50%;
                box-sizing: border-box;
            }

            .content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <button class="back-button" onclick="window.history.back()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M15.707 14.707a1 1 0 0 0 0-1.414L11.414 9l4.293-4.293a1 1 0 1 0-1.414-1.414l-5 5a1 1 0 0 0 0 1.414l5 5a1 1 0 0 0 1.414 0z" fill="#fff"/>
            </svg>
        </button>
        <h2>Menu</h2>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="user.php" class="active">Employee</a>
        <a href="student_profile.php" class="active">Student Profi</a> <!-- Mark as active -->
    </div>
    <div class="content">
        <h2>User Profile</h2>
        <div class="profile-info">
            <img src="<?php echo htmlspecialchars($user['photo']); ?>" alt="Profile Photo">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?> <?php echo htmlspecialchars($user['middle_name']); ?> <?php echo htmlspecialchars($user['last_name']); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($user['age']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
            <p><strong>Birthday:</strong> <?php echo htmlspecialchars($user['birthday']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($user['contact_number']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?></p>
        </div>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>
