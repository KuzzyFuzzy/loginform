<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_info'])) {
        // Update user info
        $name = $_POST['name'];
        $middle_name = isset($_POST['no_middle_name']) ? NULL : $_POST['middle_name'];
        $last_name = $_POST['last_name'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $birthday = $_POST['birthday'];
        $contact_number = $_POST['contact_number'];
        $address = $_POST['address'];
        $email = $_POST['email'];
        
        $sql = "UPDATE users SET 
                name=?, 
                middle_name=?, 
                last_name=?, 
                age=?, 
                gender=?, 
                birthday=?, 
                contact_number=?, 
                address=?, 
                email=? 
                WHERE id=?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("sssssssssi", $name, $middle_name, $last_name, $age, $gender, $birthday, $contact_number, $address, $email, $user_id);
        if ($stmt->execute()) {
            echo "Information updated successfully!";
        } else {
            echo "Error updating information: " . $stmt->error;
        }
        
        $stmt->close();
    } elseif (isset($_POST['delete_account'])) {
        $sql = "SELECT password FROM users WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo "User not found.";
            exit();
        }
        
        $user = $result->fetch_assoc();
        
        $password = trim($_POST['password']);
        if ($password === $user['password']) { // Direct comparison
            $sql = "DELETE FROM users WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                session_destroy();
                // Set user status to offline (0) before redirecting
                $update_status_sql = "UPDATE users SET status = 0 WHERE id = ?";
                $status_stmt = $conn->prepare($update_status_sql);
                $status_stmt->bind_param("i", $user_id);
                $status_stmt->execute();

                header("Location: login.php");
                exit();
            } else {
                echo "Error deleting account: " . $stmt->error;
            }
        } else {
            echo "Incorrect password. Account not deleted.";
        }
    }
}

// Fetch user details and status
$sql = "SELECT * FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Display user status (Online/Offline)
$status = $user['status'] == 1 ? 'Online' : 'Offline';

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            padding: 0;
            background-color: #FFD700;
        }
        h2 {
            color: #333;
        }
        form {
            background: #FFD700;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        textarea {
            width: 10%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
        textarea {
            height: 10px;
        }
        img {
            max-width: 150px;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Account Settings</h2>
    <p>Status: <?php echo htmlspecialchars($status, ENT_QUOTES); ?></p>
    <form method="post">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>" required>
        
        <label>Middle Name:</label>
        <input type="text" name="middle_name" value="<?php echo htmlspecialchars($user['middle_name'], ENT_QUOTES); ?>">
        <input type="checkbox" name="no_middle_name" value="1" <?php echo is_null($user['middle_name']) ? 'checked' : ''; ?>> I don't have a Middle Name
        
        <label>Last Name:</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name'], ENT_QUOTES); ?>" required>
        
        <label>Age:</label>
        <input type="number" name="age" value="<?php echo htmlspecialchars($user['age'], ENT_QUOTES); ?>" required>
        
        <label>Gender:</label>
        <input type="radio" name="gender" value="Male" <?php echo $user['gender'] == 'Male' ? 'checked' : ''; ?> required> Male
        <input type="radio" name="gender" value="Female" <?php echo $user['gender'] == 'Female' ? 'checked' : ''; ?> required> Female
        <input type="radio" name="gender" value="Other" <?php echo $user['gender'] == 'Other' ? 'checked' : ''; ?> required> Other
        
        <label>Birthday:</label>
        <input type="date" name="birthday" value="<?php echo htmlspecialchars($user['birthday'], ENT_QUOTES); ?>" required>
        
        <label>Contact Number:</label>
        <input type="text" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number'], ENT_QUOTES); ?>" required>
        
        <label>Address:</label>
        <textarea name="address" required><?php echo htmlspecialchars($user['address'], ENT_QUOTES); ?></textarea>
        
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>" required>
        
        <input type="submit" name="update_info" value="Update Information">
    </form>
    
    <form method="post" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
        <label>Enter your password to delete your account:</label>
        <input type="password" name="password" required>
        <input type="submit" name="delete_account" value="Delete My Account">
    </form>
    
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
