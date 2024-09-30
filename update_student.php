<?php
session_start();
include 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
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

    // Handle file upload
    $photo = null;
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/student_photos/";
        $photo = $target_dir . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }

    // Update student details in database
    $stmt = $conn->prepare("UPDATE students SET name = ?, middle_name = ?, last_name = ?, email = ?, birthday = ?, status = ?" . ($photo ? ", photo = ?" : "") . " WHERE id = ?");
    if ($photo) {
        $stmt->bind_param("sssssssi", $name, $middle_name, $last_name, $email, $birthday, $status, $photo, $id);
    } else {
        $stmt->bind_param("ssssssi", $name, $middle_name, $last_name, $email, $birthday, $status, $id);
    }
    $stmt->execute();
    $stmt->close();

    header('Location: dashboard.php');
    exit();
}

// Fetch student details for the form
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, name, middle_name, last_name, email, birthday, status, photo FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$student) {
        die("Student not found.");
    }
} else {
    die("No student ID provided.");
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 80%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group button {
            background-color: #FF1493;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }

        .form-group button:hover {
            background-color: #FF69B4;
        }

        .student-photo {
            display: block;
            margin: 10px 0;
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Student</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="middle_name">Middle Name</label>
                <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($student['middle_name']); ?>">
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="birthday">Birthday</label>
                <input type="date" id="birthday" name="birthday" value="<?php echo htmlspecialchars($student['birthday']); ?>" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="1" <?php echo $student['status'] == 1 ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo $student['status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label for="photo">Photo</label>
                <?php if (!empty($student['photo'])): ?>
                    <img src="<?php echo htmlspecialchars($student['photo']); ?>" alt="Student Photo" class="student-photo">
                <?php endif; ?>
                <input type="file" id="photo" name="photo">
            </div>
            <div class="form-group">
                <button type="submit" name="update_student">Update Student</button>
            </div>
        </form>
    </div>
</body>
</html>
