<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Ensure the cart is initialized as an array if not already
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch cart items from the session (safe check to ensure it's an array)
$cart_items = $_SESSION['cart'];

// Check if the form to add to cart was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract data from the POST request
    $equipment_id = $_POST['equipment_id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    // Create an array for the new cart item
    $cart_item = [
        'equipment_id' => $equipment_id,
        'name' => $name,
        'category' => $category,
        'price' => $price,
        'image' => $image
    ];

    // Add the cart item to the session cart (array)
    $_SESSION['cart'][] = $cart_item;

    // Optionally, you can redirect the user to the cart page after adding the item
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - FiTFuEL</title>
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
            color: white;
            min-height: 100vh;
            padding: 0;
            margin: 0;
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

        /* Cart Page Styles */
        .cart-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.23); /* Semi-transparent background */
            border-radius: 15px; /* Rounded corners for the container */
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 80%;
            margin: 15px 0;
            background-color: rgba(255, 255, 255, 0.23); /* Semi-transparent background for cart items */
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(247, 243, 243, 0.23);
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .cart-item .info {
            flex: 1;
            margin-left: 15px;
        }

        .cart-item .price {
            font-weight: bold;
        }

        .remove-btn {
            background-color:rgb(0, 0, 0);
            color: white;
            padding: 5px 15px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .remove-btn:hover {
            background-color:rgb(0, 0, 0);
        }

        .cart-total {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
            color: #fff;
        }

        /* Add a slight glow effect for better readability */
        .cart-item h3, .cart-item .price {
            text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.44);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">FiTFuEL</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="cart.php">Cart</a>
        </div>
    </nav>

    <div class="cart-container">
        <?php
        if (count($cart_items) > 0) {
            $total_price = 0;
            foreach ($cart_items as $cart_item) {
                $total_price += $cart_item['price'];
        ?>
            <div class="cart-item">
                <img src="<?php echo htmlspecialchars($cart_item['image']); ?>" alt="<?php echo htmlspecialchars($cart_item['name']); ?>">
                <div class="info">
                    <h3><?php echo htmlspecialchars($cart_item['name']); ?></h3>
                    <p class="price">₹<?php echo htmlspecialchars($cart_item['price']); ?></p>
                </div>
                <form method="POST" action="remove_from_cart.php">
                    <input type="hidden" name="equipment_id" value="<?php echo htmlspecialchars($cart_item['equipment_id']); ?>">
                    <button type="submit" class="remove-btn">Remove</button>
                </form>
            </div>
        <?php
            }
            echo "<div class='cart-total'>Total: ₹" . $total_price . "</div>";
        } else {
            echo "<p>Your cart is empty.</p>";
        }
        ?>
    </div>

</body>
</html>
