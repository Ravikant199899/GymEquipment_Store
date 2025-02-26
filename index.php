<?php
// db.php: Database connection setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$db   = 'GYM_EQUIPMENT_STORE';
$user = 'root';
$pass = ''; // Replace with your MySQL password

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create tables if not exist
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Equipment Store - User Authentication</title>
    <!-- Include Font Awesome for the Eye Icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: url('images/equipment.jpg') no-repeat center center fixed;
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            color: #fff;
        }

        .container {
            background: rgba(5, 5, 5, 0.7);
            padding: 40px 35px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            transition: all 0.3s ease-in-out;
        }

        .container:hover {
            transform: scale(1.05);
        }

        h2 {
            text-align: center;
            font-size: 28px;
            color: #fff;
            margin-bottom: 30px;
            font-weight: 700;
        }

        input {
            width: 100%;
            padding: 14px;
            margin: 10px 0;
            border: 2px solid #444;
            border-radius: 8px;
            background: #333;
            font-size: 16px;
            color: #fff;
            transition: 0.3s ease;
        }

        input:focus {
            border-color: #d32f2f;
            background: #444;
            outline: none;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #d32f2f;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background: #c62828;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 16px;
            color: #d32f2f;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s ease;
        }

        a:hover {
            color: #FFC107;
        }

        .password-container {
            position: relative;
            width: 100%;
        }

        #eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 20px;
        }

        .alert {
            padding: 10px;
            margin-top: 20px;
            border-radius: 6px;
            font-weight: 500;
        }

        .alert.success {
            background-color: #2ecc71;
            color: #fff;
        }

        .alert.error {
            background-color: #e74c3c;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container">
    <?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['register'])) {
            // Registration logic
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into database
            $stmt = $conn->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
            $stmt->bind_param('ss', $email, $hashed_password);

            if ($stmt->execute()) {
                echo "<p style='color:green;'>Registration successful!</p>";
            } else {
                echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } elseif (isset($_POST['login'])) {
            // Login logic
            $email = $_POST['email'];
            $password = $_POST['password'];

            $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) { // Verify the hashed password
                $_SESSION['user'] = $user;
                // Redirect to home.php after successful login
                header('Location: home.php');
                exit;
            } else {
                echo "<p style='color:red;'>Invalid email or password.</p>";
            }
            $stmt->close();
        } elseif (isset($_POST['forgot_password'])) {
            // Forgot password logic
            $email = $_POST['email'];
            $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                $_SESSION['reset_email'] = $email;
                header('Location: ?action=reset_password');
                exit;
            } else {
                echo "<p style='color:red;'>No user found with that email address.</p>";
            }
            $stmt->close();
        } elseif (isset($_POST['reset_password'])) {
            // Reset password logic
            if (isset($_SESSION['reset_email'])) {
                $email = $_SESSION['reset_email'];
                $new_password = $_POST['new_password'];

                // Hash the new password
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare('UPDATE users SET password = ? WHERE email = ?');
                $stmt->bind_param('ss', $hashed_new_password, $email);

                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    unset($_SESSION['reset_email']);
                    echo "<p style='color:green;'>Password successfully reset!</p>";
                } else {
                    echo "<p style='color:red;'>Error: Could not reset password.</p>";
                }
                $stmt->close();
            } else {
                echo "<p style='color:red;'>Invalid reset attempt.</p>";
            }
        }
    }

    if (!isset($_GET['action']) || $_GET['action'] === 'login') {
    ?>
        <h2>Login</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span id="eye-icon" class="fa fa-eye" onclick="togglePassword()"></span>
            </div>
            
            <button type="submit" name="login">Login</button>
            <a href="register.php">Don't have an account? Register</a>
            <a href="?action=forgot_password">Forgot Password?</a>
        </form>
    <?php
    } elseif (isset($_GET['action']) && $_GET['action'] === 'forgot_password') {
    ?>
        <h2>Forgot Password</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="forgot_password">Submit</button>
            <a href="?action=login">Back to Login</a>
        </form>
    <?php
    } elseif (isset($_GET['action']) && $_GET['action'] === 'reset_password') {
    ?>
        <h2>Reset Password</h2>
        <form method="POST">
            <input type="password" name="new_password" placeholder="Enter new password" required>
            <button type="submit" name="reset_password">Reset Password</button>
            <a href="?action=login">Back to Login</a>
        </form>
    <?php
    }
    ?>
</div>

<!-- JavaScript for Password Toggle -->
<script>
    function togglePassword() {
        var passwordField = document.getElementById("password");
        var eyeIcon = document.getElementById("eye-icon");

        // Toggle the password field type
        if (passwordField.type === "password") {
            passwordField.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }
    }
</script>
</body>
</html>
