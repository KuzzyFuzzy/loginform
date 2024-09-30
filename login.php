<?php
session_start();
include 'database.php';

// Initialize error message variable
$error_message = "";

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
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

// Generate a simple math question for CAPTCHA
$num1 = rand(1, 10);
$num2 = rand(1, 10);
$_SESSION['captcha_answer'] = $num1 + $num2;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_answer = $_POST['captcha_answer'];

    if ($user_answer == $_SESSION['captcha_answer']) {
        // Process login (add your authentication logic here)
        echo "Login successful!";
        // Reset attempts on successful login
        $_SESSION['attempts'] = 0;
    } else {
        $_SESSION['attempts']++;
        $_SESSION['last_attempt_time'] = time(); // Record the time of the last attempt
        echo "Incorrect CAPTCHA answer. Attempts left: " . (3 - $_SESSION['attempts']);
    }
}

function handleLogin($conn) {
    global $error_message;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (empty($email) || empty($password)) {
            $error_message = "Please enter both email and password.";
            return;
        }

        $sql = "SELECT id, password, session_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $stored_password, $stored_session_id);
            $stmt->fetch();

            // Directly compare plaintext passwords
            if ($password === $stored_password) {
                if ($stored_session_id && $stored_session_id !== session_id()) {
                    $_SESSION['already_logged_in'] = true;  // Set session variable to indicate multiple logins
                    header("Location: login.php");
                    exit();
                }

                $session_id = session_id();
                $updateStatusSql = "UPDATE users SET status = 1, session_id = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateStatusSql);
                $updateStmt->bind_param("si", $session_id, $id);
                $updateStmt->execute();
                $updateStmt->close();

                $_SESSION['user_id'] = $id;
                $_SESSION['email'] = $email;

                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }

        $stmt->close();
        $conn->close();
    }
}

handleLogin($conn);

// Check if the user was already logged in elsewhere
if (isset($_SESSION['already_logged_in']) && $_SESSION['already_logged_in']) {
    $error_message = "Your account is already logged in from another location.";
    unset($_SESSION['already_logged_in']);  // Clear the session variable
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('upload/5.jfif');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #333;
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            position: relative;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            margin: 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="email"], input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-bottom: 15px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .links {
            text-align: center;
            margin-top: 15px;
        }
        .links a {
            color: #007BFF;
            text-decoration: none;
            margin: 0 10px;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 16px;
            text-align: center;
        }
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    color: #333;
    margin: 0;
    padding: 20px;
}

.container {
    max-width: 400px;
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

label {
    display: block;
    margin-top: 10px;
}

input[type="text"], input[type="email"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

button {
    background-color: #ff6f61;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 15px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
}

button:hover {
    background-color: #e55b4a;
}

.student-image {
    text-align: center;
    margin-bottom: 15px;
}

.student-image img {
    max-width: 100%;
    border-radius: 5px; /* Optional: to make the image corners rounded */
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <!-- Display error message if set -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Email:</label><br>
            <input type="email" name="email" required><br>
            <label>Password:</label><br>
            <input type="password" name="password" required><br>
             <label for="captcha"><?php echo "$num1 + $num2 = ?"; ?></label>
        <input type="text" name="captcha_answer" required>
            <input type="submit" value="Login"><br>
        </form>
        <div class="links">
            <a href="register.php">Register</a> | 
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>

    <script>
        // Display native alert if the error message indicates multiple login
        window.addEventListener('DOMContentLoaded', (event) => {
            const errorMessage = '<?php echo addslashes($error_message); ?>';
            if (errorMessage.includes('already logged in')) {
                alert(errorMessage);
            }
        });
    </script>
</body>
</html>
