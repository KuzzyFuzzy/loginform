<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Redirect to the reset password page
        header("Location: reset_password.php?email=$email");
        exit();
    } else {
        echo "No user found with this email!";
    }

 if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
}
if (!isset($_SESSION['last_attempt_time'])) {
    $_SESSION['last_attempt_time'] = time();
}

// Check for too many attempts
if ($_SESSION['attempts'] >= 3 && (time() - $_SESSION['last_attempt_time']) < 15) {
    $wait_time = 15 - (time() - $_SESSION['last_attempt_time']);
    die("Too many failed attempts. Please wait {$wait_time} seconds before trying again.");
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'my_databasekay');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch students from the database
$sql = "SELECT id, name, photo FROM students WHERE status = 1"; // Assuming status 1 means active
$result = $conn->query($sql);

// Store all students in an array
$students = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
} else {
    die("Error fetching student data from the database. Please try again later.");
}

// Check for enough students
if (count($students) < 3) {
    die("Not enough students in the database for CAPTCHA.");
}

// Randomly select a student for the CAPTCHA
$random_student = $students[array_rand($students)];

// Prepare a list of options for selection (include the correct answer)
$options = [$random_student['name']];
while (count($options) < 3) {
    $other_student = $students[array_rand($students)];
    if (!in_array($other_student['name'], $options)) {
        $options[] = $other_student['name'];
    }
}
shuffle($options); // Shuffle options for randomness

// Check for form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_answer = $_POST['name'];

    if ($user_answer == $random_student['name']) {
        // Process password reset (add your logic here)
        echo "Password reset link sent to your email!";
        // Reset attempts on successful reset
        $_SESSION['attempts'] = 0;
        $_SESSION['last_attempt_time'] = time(); // Reset time
    } else {
        $_SESSION['attempts']++;
        $_SESSION['last_attempt_time'] = time(); // Record the time of the last attempt
        echo "Incorrect answer. Attempts left: " . (3 - $_SESSION['attempts']);
    }
}

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <style>
        /* General styles */
body {
    font-family: 'Arial', sans-serif;
    background: linear-gradient(120deg, #ffafbd, #ffc3a0);
    padding: 50px;
    text-align: center;
    color: #333;
    transition: background 1s ease;
}

/* Heading style */
h2 {
    font-size: 2.5em;
    color: #fff;
    margin-bottom: 20px;
    animation: fadeInDown 1.5s ease;
}

/* Form styling */
form {
    background: rgba(255, 255, 255, 0.8);
    padding: 20px;
    border-radius: 8px;
    display: inline-block;
    max-width: 400px;
    width: 100%;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    animation: slideInUp 1.5s ease;
}

/* Label styling */
label {
    font-size: 1.2em;
    color: #555;
    margin-bottom: 10px;
    display: block;
    transition: color 0.3s ease;
}

/* Input styling */
input[type="email"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 2px solid #ffc3a0;
    border-radius: 6px;
    font-size: 1em;
    box-sizing: border-box;
    transition: border 0.3s ease;
}

input[type="submit"] {
    width: 100%;
    background-color: #ffafbd;
    color: white;
    padding: 12px;
    font-size: 1.2em;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

/* Hover effects */
input[type="submit"]:hover {
    background-color: #ffc3a0;
    transform: scale(1.05);
}

/* Input focus effects */
input[type="email"]:focus {
    border-color: #ffafbd;
    outline: none;
}

/* Link styling */
a {
    color: #fff;
    text-decoration: none;
    margin-top: 10px;
    display: block;
    transition: color 0.3s ease;
}

a:hover {
    color: #ffafbd;
}

/* Animations */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    </style>
    <title>Forgot Password</title>
</head>

<body>
    <h2>Forgot Password</h2>
    <form method="post">
        <label>Email:</label><br>
        <input type="email" name="email" required><br>
    <img src="<?php echo $random_student['photo']; ?>" alt="Student Photo" class="student-photo">
    <form method="POST">
        <div class="options">
            <?php foreach ($options as $option): ?>
                <label>
                    <input type="radio" name="student_name" value="<?php echo $option; ?>" required>
                    <?php echo $option; ?>
                </label>
            <?php endforeach; ?>
        <input type="submit" value="Continue"><br>
    </form><br>
    <br>
    Don't have an account?  <a href="register.php">Register Here</a>

    Already have an account? <a href="login.php">Login Here</a>
</body>
</html>
