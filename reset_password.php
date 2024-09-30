<?php
include 'database.php';

// Function to handle password reset form submission
function handlePasswordReset($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
        $email = $_POST['email'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];

        // Check if the new password and confirmation match
        if ($new_password !== $confirm_new_password) {
            echo "Passwords do not match!";
            return;
        }

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $sql = "UPDATE users SET password=?, status=1 WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            echo "Password reset successful!";
        } else {
            echo "Error resetting password: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Function to display the password reset form
function displayPasswordResetForm($conn) {
    if (isset($_GET['email'])) {
        $email = $_GET['email'];
        $sql = "SELECT id FROM users WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Display the password reset form
            ?>

            <!DOCTYPE html>
            <html>
            <head>
                <title>Reset Password</title>
                <style>
                    /* Basic styles for the reset password form */
                    body {
                        font-family: Arial, sans-serif;
                        background: linear-gradient(135deg, #FFC3A0, #ACB6E5);
                        color: #333;
                        padding: 50px;
                        transition: background 1s ease-in-out;
                    }
                    h2 {
                        text-align: center;
                        font-size: 2.5em;
                        color: #fff;
                        animation: fadeIn 1.5s ease-in-out;
                    }
                    form {
                        background: rgba(255, 255, 255, 0.8);
                        padding: 20px;
                        border-radius: 10px;
                        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
                        max-width: 400px;
                        margin: 0 auto;
                        animation: slideIn 1.2s ease-in-out;
                    }
                    label {
                        font-size: 1.2em;
                        color: #555;
                        transition: color 0.3s ease;
                    }
                    input[type="password"] {
                        width: 100%;
                        padding: 10px;
                        margin: 5px 0 20px;
                        border: 2px solid #ACB6E5;
                        border-radius: 5px;
                        font-size: 1em;
                        box-sizing: border-box;
                        transition: border 0.3s ease;
                    }
                    input[type="submit"] {
                        width: 100%;
                        background-color: #ACB6E5;
                        color: white;
                        padding: 10px;
                        font-size: 1.2em;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                        transition: background-color 0.3s ease, transform 0.3s ease;
                    }
                    input[type="submit"]:hover {
                        background-color: #74ebd5;
                        transform: scale(1.05);
                    }
                    @keyframes slideIn {
                        from {
                            transform: translateY(-100%);
                            opacity: 0;
                        }
                        to {
                            transform: translateY(0);
                            opacity: 1;
                        }
                    }
                    @keyframes fadeIn {
                        from {
                            opacity: 0;
                        }
                        to {
                            opacity: 1;
                        }
                    }
                </style>
            </head>
            <body>
                <h2>Reset Password</h2>
                <form method="post" onsubmit="return confirm('Are you sure you want to reset your password?');">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <label>New Password:</label><br>
                    <input type="password" name="new_password" required><br>
                    <label>Confirm New Password:</label><br>
                    <input type="password" name="confirm_new_password" required><br>
                    <input type="submit" value="Reset Password">
                </form>
            </body>
            </html>

            <?php
        } else {
            echo "No user found with this email!";
        }

        $stmt->close();
    } else {
        echo "No email provided!";
    }
}

// Check which action to take
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    handlePasswordReset($conn);
} else {
    displayPasswordResetForm($conn);
}

// Optionally: Update the user's status to offline (0) when they access the reset page
if (isset($_GET['email'])) {
    $email = $_GET['email'];
    $sql = "UPDATE users SET status=0 WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>
