<?php
session_start();
include 'database.php'; // Ensure this file contains the necessary database connection code

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $middle_name = isset($_POST['no_middle_name']) ? 'N/A' : $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format');</script>";
        exit();
    }

    // Check if email already exists
    $email_check_sql = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($email_check_sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Oops, this email is already taken');</script>";
        $stmt->close();
        $conn->close();
        exit();
    }

    // Handle file upload
    $target_dir = "uploads/";
    $photo = $target_dir . basename($_FILES["photo"]["name"]);

    // Ensure the upload directory exists
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0755, true)) {
            die("Failed to create directory.");
        }
    }

    // Check if file upload is successful
    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $photo)) {
        // Directly use the plaintext password (not recommended for production)
        $plain_password = $password;

        // Prepare and execute the insert query
        $sql = "INSERT INTO users (name, middle_name, last_name, age, gender, birthday, contact_number, address, photo, email, password, login_attempts, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("sssssssssss", $name, $middle_name, $last_name, $age, $gender, $birthday, $contact_number, $address, $photo, $email, $plain_password);
        if ($stmt->execute()) {
            // Automatically log the user in and redirect to the dashboard
            $user_id = $conn->insert_id;
            $_SESSION['user_id'] = $user_id;
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error executing query: " . $stmt->error;
        }
    } else {
        echo "<script>alert('Failed to upload photo');</script>";
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
            background-color: #FFD700;
           
        }

        /* Heading style */
        h2 {
            text-align: center;
            font-size: 2.5em;
            color: #fff;
            animation: fadeIn 1.5s ease-in-out;
        }

        /* Form styling */
        form {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            margin: 0 auto;
            animation: slideIn 1.2s ease-in-out;
        }

        /* Label and input styling */
        label {
            font-size: 1.2em;
            color: #555;
            transition: color 0.3s ease;
        }

        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="date"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px;
            border: 2px solid #ACB6E5;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
            transition: border 0.3s ease;
        }

        input[type="radio"] {
            margin: 10px;
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

        /* Hover effects */
        input[type="submit"]:hover {
            background-color: #74ebd5;
            transform: scale(1.05);
        }

        /* Input focus effects */
        input:focus, textarea:focus {
            border-color: #74ebd5;
            outline: none;
        }

        label:hover {
            color: #74ebd5;
        }

        /* Link styling */
        a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #ACB6E5;
        }

        /* Keyframe animations */
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
    <title>Register</title>
    <script>
    function toggleMiddleNameField() {
        const middleNameField = document.querySelector('input[name="middle_name"]');
        const noMiddleNameCheckbox = document.querySelector('input[name="no_middle_name"]');

        if (noMiddleNameCheckbox.checked) {
            middleNameField.value = 'N/A';
            middleNameField.disabled = true;
            middleNameField.classList.add('valid');  // Add valid class for the green outline
            middleNameField.classList.remove('invalid');
        } else {
            middleNameField.value = '';
            middleNameField.disabled = false;
            middleNameField.classList.remove('valid');
            middleNameField.classList.remove('invalid');
        }
        validateForm();
    }

    function validateForm() {
        const formElements = document.querySelectorAll('input, textarea');
        let isFormComplete = true;

        formElements.forEach(element => {
            if (element.required && !element.disabled) {
                if (element.value.trim() === '') {
                    element.classList.remove('valid');
                    element.classList.add('invalid');
                    isFormComplete = false;
                } else {
                    element.classList.remove('invalid');
                    element.classList.add('valid');
                }
            } else if (element.disabled && element.value.trim() === 'N/A') {
                element.classList.remove('invalid');
                element.classList.add('valid');
            }
        });

        // Ensure the submit button has the correct state
        document.querySelector('input[type="submit"]').disabled = !isFormComplete;
    }

    function validateAge(event) {
        const input = event.target;
        const value = input.value;
        // Remove non-numeric characters except for empty strings
        input.value = value.replace(/[^0-9]/g, '');
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Initial form validation
        validateForm();

        // Add event listener for input changes
        document.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('input', validateForm);
        });

        // Add event listener for checkbox change
        document.querySelector('input[name="no_middle_name"]').addEventListener('change', toggleMiddleNameField);

        // Add event listener to restrict age input
        document.querySelector('input[name="age"]').addEventListener('input', validateAge);
    });
    </script>
</head>
<body>
    <h2>Register</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Name:</label><br>
        <input type="text" name="name" required><br>
        
        <label>Middle Name:</label><br>
        <input type="text" name="middle_name"><br>
        <input type="checkbox" name="no_middle_name" value="1" onchange="toggleMiddleNameField()"> I don't have a Middle Name<br>
        
        <label>Last Name:</label><br>
        <input type="text" name="last_name" required><br>
        
        <label>Age:</label><br>
        <input type="number" name="age" required><br>
        
        <label>Gender:</label><br>
        <input type="radio" name="gender" value="Male" required> Male
        <input type="radio" name="gender" value="Female" required> Female
        <input type="radio" name="gender" value="Other" required> Other<br>
        
        <label>Birthday:</label><br>
        <input type="date" name="birthday" required><br>
        
        <label>Contact Number:</label><br>
        <input type="text" name="contact_number" id="contact_number" required
               pattern="\d{11}" maxlength="11" inputmode="numeric"
               oninput="this.value = this.value.replace(/[^0-9]/g, '')"><br>

        <label>Address:</label><br>
        <textarea name="address" required></textarea><br>
        
        <label>Photo:</label><br>
        <input type="file" name="photo" required><br>
        
        <label>Email:</label><br>
        <input type="email" name="email" required><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" required><br>
        
        <input type="submit" value="Register">
    </form>
    Already have an account? <a href="login.php">Login Here</a>
</body>
</html>
