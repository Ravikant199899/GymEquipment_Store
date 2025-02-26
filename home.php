<?php
session_start();

// PHP connection and queries remain the same
$host = 'localhost';
$db   = 'GYM_EQUIPMENT_STORE';  // Your database name
$user = 'root';  // Your database username
$pass = '';      // Your database password

// Create database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch gym equipment from the database
$query = "SELECT * FROM equipments";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FiTFuEL</title>
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: url('images/gym-background.png') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }

        h3 {
            margin-bottom: 10px;
            font-size: 18px;
            font-weight: 600;
        }

        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background-color: rgba(34, 34, 34, 0.8); /* Semi-transparent for background visibility */
            color: white;
            position: relative;
            z-index: 10; /* Ensure navbar stays on top */
        }

        .navbar .logo {
            font-size: 50px;
            font-weight: 700;
        }

        .navbar .nav-links a {
            margin: 0 15px;
            text-decoration: none;
            color: white;
            font-size: 16px;
            font-weight: 500;
            transition: 0.3s;
        }

        .navbar .nav-links a:hover {
            color: #ff6600;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
            display: inline-block;
            z-index: 100; /* Ensure profile dropdown stays on top of other elements */
        }

        .profile-btn {
            background-color: #ffffff; /* White background */
            border: 2px solid #000000; /* Black border for visibility */
            color: #000000; /* Black text */
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s; /* Smooth transition */
        }

        .profile-btn:hover {
            background-color: #000000; /* Black background on hover */
            color: #ffffff; /* White text on hover */
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: rgba(34, 34, 34, 0.9);
            color: white;
            min-width: 160px;
            border-radius: 5px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            padding: 10px;
        }

        .profile-dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            color: white;
            padding: 8px;
            text-decoration: none;
            display: block;
            font-size: 14px;
        }

        .dropdown-content a:hover {
            background-color: #575757;
        }

        .dropdown-content p {
            margin: 0;
            font-size: 14px;
            color: #ccc;
        }

        /* Equipment Listing */
        .equipment-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Make the cards a bit smaller */
            gap: 25px;  /* Add space between product containers */
            padding: 50px 30px; /* Add padding to the container to make background visible */
            z-index: 1; /* Make sure product containers are above the background */
        }

        .equipment-card {
            background-color: rgba(255, 255, 255, 0.32); /* Slight transparency for background visibility */
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            overflow: hidden;
            position: relative;
        }

        .equipment-card:hover {
            transform: translateY(-10px);
        }

        .equipment-image {
            width: 100%;
            height: 200px; /* Reduced height for better background visibility */
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .equipment-info {
            text-align: center;
        }

        .price {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .btn-add-to-cart {
            padding: 10px 15px;
            background-color:rgb(39, 39, 39);
            color: white;
            font-size: 16px;
            font-weight: 500;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-add-to-cart:hover {
            background-color: #cc5500;
        }

        /* Search Bar Styles */
        .search-bar form {
            display: flex;
            align-items: center;
        }

        .search-bar input[type="text"] {
            padding: 12px 20px;  /* Increase padding for bigger size */
            font-size: 18px;  /* Increase font size for better readability */
            width: 350px;  /* Increase width of the search bar */
            border: 2px solid #ccc;  /* Border for input */
            border-radius: 25px;  /* Rounded corners for input */
            margin-right: 10px;  /* Space between search input and button */
            transition: all 0.3s ease;  /* Smooth transition */
        }

        .search-bar input[type="submit"] {
            padding: 12px 20px;
            font-size: 16px;
            background-color: #ff6600;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-bar input[type="submit"]:hover {
            background-color: #cc5500;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">FitFuEL</div>
        <!-- Search Bar -->
        <div class="search-bar">
            <form action="search.php" method="GET">
                <input type="text" name="search" placeholder="Search equipment...">
                <input type="submit" value="Search">
            </form>
        </div>
        <div class="nav-links">
            <?php
            // Check if user is logged in and display user profile
            if (isset($_SESSION['user'])) {
                $userEmail = $_SESSION['user']['email']; // Assuming user data is stored in session
                ?>
                <!-- Profile Button with Dropdown -->
                <div class="profile-dropdown">
                    <button class="profile-btn"><?php echo htmlspecialchars($userEmail); ?> ▼</button>
                    <div class="dropdown-content">
                        <p><?php echo htmlspecialchars($userEmail); ?></p>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
                <?php
            }
            ?>
            <a href="cart.php">Cart</a>
        </div>
    </nav>

    <!-- Equipment List -->
    <section class="equipment-container">
        <?php
        // Fetch gym equipment from the database
        if ($result->num_rows > 0) {
            while ($equipment = $result->fetch_assoc()) { ?>
                <div class="equipment-card">
                    <img src="<?php echo htmlspecialchars($equipment['image']); ?>" alt="<?php echo htmlspecialchars($equipment['name']); ?>" class="equipment-image">
                    <div class="equipment-info">
                        <h3><?php echo htmlspecialchars($equipment['name']); ?></h3>
                        <p><?php echo htmlspecialchars($equipment['category']); ?></p>
                        <p class="price">₹<?php echo htmlspecialchars($equipment['price']); ?></p>
                        <p><?php echo htmlspecialchars($equipment['description']); ?></p>
                        
                        <!-- Add to Cart Form -->
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="equipment_id" value="<?php echo htmlspecialchars($equipment['id']); ?>">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($equipment['name']); ?>">
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($equipment['category']); ?>">
                            <input type="hidden" name="price" value="<?php echo htmlspecialchars($equipment['price']); ?>">
                            <input type="hidden" name="image" value="<?php echo htmlspecialchars($equipment['image']); ?>">
                            <button type="submit" class="btn-add-to-cart">Add to Cart</button>
                        </form>
                    </div>
                </div>
            <?php }
        } else {
            echo "<p>No equipment available.</p>";
        }

        // Close connection
        $conn->close();
        ?>
    </section>
</body>
</html>