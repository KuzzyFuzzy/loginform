<?php
session_start();
include 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You need to log in first.'); window.location.href = 'login.php';</script>";
    exit();
}

// Initialize variables for form values and errors
$id = '';
$name = '';
$middle_name = '';
$last_name = '';
$birthday = '';
$email = '';
$status = 1; // Default to active
$photo = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $birthday = trim($_POST['birthday']); // Added birthday
    $status = intval($_POST['status']);

    // Handle file upload
    $target_dir = "uploads/student_photos/";
    $photo = $target_dir . basename($_FILES["photo"]["name"]);

    // Ensure the upload directory exists
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0755, true)) {
            die("Failed to create directory.");
        }
    }

    // Validate email
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || !strstr($email, '.com')) {
        $error = 'Invalid email format. Email must contain "@" and ".com".';
    } else {
        // Check for errors before proceeding
        if (empty($error)) {
            // Prepare SQL query to insert new student
            $stmt = $conn->prepare("INSERT INTO students (id, name, middle_name, last_name, email, birthday, status, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $id, $name, $middle_name, $last_name, $email, $birthday, $status, $photo);

            if ($stmt->execute()) {
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Failed to add student.';
            }

            $stmt->close();
        }
    }
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            margin-top: 0;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="file"], select, input[type="date"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0 20px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        button {
            background-color: #FF1493;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #FF69B4;
        }

        .error {
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Add Student</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="add_student.php" method="post" enctype="multipart/form-data">
            <label for="id">ID:</label>
            <input type="text" id="id" name="id" value="<?php echo htmlspecialchars($id); ?>" required>

            <label for="name">First Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

            <label for="middle_name">Middle Name:</label>
            <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($middle_name); ?>">

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="birthday">Birthday:</label>
            <input type="date" id="birthday" name="birthday" value="<?php echo htmlspecialchars($birthday); ?>" required>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="1" <?php echo $status == 1 ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo $status == 0 ? 'selected' : ''; ?>>Inactive</option>
            </select>

            <label for="photo">Photo:</label>
            <input type="file" id="photo" name="photo">

            <button type="submit">Add Student</button>
        </form>
    </div>
</body>
</html>
