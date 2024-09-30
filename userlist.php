<?php
session_start();
include 'database.php';

// Function to get status text
function getStatusText($status) {
    return $status == 1 ? 'Active' : 'Inactive';
}

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

// Handle account switch
if (isset($_GET['switch']) && isset($_GET['user_id'])) {
    $new_user_id = intval($_GET['user_id']);

    if ($new_user_id != $_SESSION['user_id']) {
        session_destroy();
        session_start();

        $_SESSION['user_id'] = $new_user_id;

        header('Location: dashboard.php');
        exit();
    }
}

// Handle student update
if (isset($_POST['update_student'])) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $status = intval($_POST['status']);

    $stmt = $conn->prepare("UPDATE students SET name = ?, middle_name = ?, last_name = ?, email = ?, birthday = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $name, $middle_name, $last_name, $email, $birthday, $status, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: dashboard.php');
    exit();
}

// Handle student delete
if (isset($_GET['delete_student'])) {
    $id = intval($_GET['delete_student']);

    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header('Location: userlist.php');
    exit();
}

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = '%' . $search . '%';

// Query to fetch all students when there is no search term
$student_sql = "SELECT id, name, middle_name, last_name, email, birthday, status, photo FROM students";
if (!empty($search)) {
    $student_sql .= " WHERE name LIKE ? OR middle_name LIKE ? OR last_name LIKE ? OR email LIKE ?";
}
$student_sql .= " ORDER BY id"; // Optional: Order by ID or any other field

// Fetch student data
$stmt = $conn->prepare($student_sql);
if (!empty($search)) {
    $stmt->bind_param("ssss", $search_query, $search_query, $search_query, $search_query);
}
$stmt->execute();
$student_result = $stmt->get_result();
$students_found = $student_result->num_rows > 0;

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
        /* Existing styles remain unchanged */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
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

        .sidebar a:hover {
            background-color: #FF1493;
        }

        .sidebar .active {
            background-color: #FF1493;
        }

        .search-bar {
            position: fixed;
            top: 66px;
            left: 260px; /* Adjusted to make space for the menu button */
            z-index: 2;
            padding: 10px;
        }

        .search-bar input[type="text"] {
            width: 230px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .content {
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s ease;
            height: calc(100vh - 40px);
            position: relative;
            z-index: 1;
        }

        h1, h2 {
            margin: 0;
            color: #333;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #FF1493;
            color: white;
        }

        .add-student-btn {
            display: block;
            background-color: #FF1493;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            margin: 20px auto;
            text-align: center;
            width: fit-content;
        }

        .add-student-btn:hover {
            background-color: #FF69B4;
        }

        .action-btn {
            display: inline-block;
            padding: 5px 10px;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }

        .update-btn {
            background-color: #FF8C00;
        }

        .delete-btn {
            background-color: #FF0000;
        }

        .update-btn:hover {
            background-color: #FF6347;
        }

        .delete-btn:hover {
            background-color: #DC143C;
        }
        
        .student-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            vertical-align: middle;
        }

        .no-results {
            text-align: center;
            color: #FF1493;
            font-size: 18px;
            margin-top: 20px;
            display: none; /* Hide by default */
        }
    </style>
</head>
<body>
    <button class="menu-btn" onclick="toggleSidebar()">☰</button>
    <button class="logout-btn" onclick="confirmLogout()" style="background-image: url('<?php echo $profileImage; ?>');"></button>
    <div id="sidebar" class="sidebar">
        <div class="nav-title">User</div>
        <a href="dashboard.php">Dashboard</a>
        <a href="userlist.php" class="active">User</a>
        <a href="video_gallery.php">Video Gallery</a>
        
    </div>


    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Search...">
    </div>
<div class="content">
        <h1>User List</h1>
        <a href="add_student.php" class="add-student-btn">Add New Student</a>
        <div class="no-results">No results found</div>
        <table id="student-table">
            <tbody>
                <?php if ($students_found): ?>
                    <?php while ($student = $student_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['id']); ?></td>
                        <td>
                            <?php if (!empty($student['photo']) && file_exists($student['photo'])): ?>
                                <img src="<?php echo htmlspecialchars($student['photo']); ?>" alt="Profile Picture" class="student-pic">
                            <?php else: ?>
                                <img src="uploads/student_photos/default.png" alt="Profile Picture" class="student-pic">
                            <?php endif; ?>
                            <?php echo htmlspecialchars($student['name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['birthday']); ?></td>
                        <td><?php echo getStatusText($student['status']); ?></td>
                        <td>
                            <a href="update_student.php?id=<?php echo $student['id']; ?>" class="action-btn update-btn">Update</a>
                            <a href="?delete_student=<?php echo $student['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
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

        // Live Search Functionality
        document.getElementById('search-input').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#student-table tbody tr');
            const noResultsMessage = document.querySelector('.no-results');
            let anyResults = false;

            rows.forEach(row => {
                const cells = row.getElementsByTagName('td');
                let isMatch = false;

                for (let i = 0; i < cells.length - 1; i++) { // Exclude the last column (Actions)
                    if (cells[i].textContent.toLowerCase().includes(query)) {
                        isMatch = true;
                        break;
                    }
                }

                row.style.display = isMatch ? '' : 'none';
                if (isMatch) {
                    anyResults = true;
                }
            });

            // Show or hide the "No results found" message
            noResultsMessage.style.display = anyResults ? 'none' : 'block';
        });

        // Initial check to display "No results found" based on PHP
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('#student-table tbody tr');
            const noResultsMessage = document.querySelector('.no-results');
            const studentsFound = <?php echo json_encode($students_found); ?>;

            // Initial display of "No results found" based on PHP data
            noResultsMessage.style.display = studentsFound ? 'none' : 'block';
        });
    </script>
</body>
</html>
