<?php
// db.php: Database connection setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Registration logic
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm']; // Password confirmation field

    // Check if the passwords match
    if ($password !== $password_confirm) {
        $error_message = "Passwords do not match!";
    } else {
        // Check if the email already exists
        $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Email already registered!";
        } else {
            // Hash the password before saving
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into database
            $stmt = $conn->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
            $stmt->bind_param('ss', $email, $hashed_password);

            if ($stmt->execute()) {
                $success_message = "Registration successful! You can now <a href='index.php'>login</a>.";
            } else {
                $error_message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Equipment Store - Register</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
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

        .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Register</h2>

    <?php
    if (isset($success_message)) {
        echo "<div class='alert success'>$success_message</div>";
    }
    if (isset($error_message)) {
        echo "<div class='alert error'>$error_message</div>";
    }
    ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        
        <!-- Password input -->
        <div style="position: relative;">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <i class="fas fa-eye eye-icon" id="togglePassword"></i>
        </div>
        
        <!-- Password Confirmation input -->
        <div style="position: relative;">
            <input type="password" name="password_confirm" id="password_confirm" placeholder="Confirm Password" required>
            <i class="fas fa-eye eye-icon" id="toggleConfirmPassword"></i>
        </div>

        <button type="submit" name="register">Register</button>
        <a href="index.php">Already have an account? Login</a>
    </form>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('password');
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;
    });

    // Toggle confirm password visibility
    document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
        const passwordConfirmField = document.getElementById('password_confirm');
        const type = passwordConfirmField.type === 'password' ? 'text' : 'password';
        passwordConfirmField.type = type;
    });
</script>
</body>
</html>
