<?php
session_start();

// Ensure the cart is initialized as an array if not already
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Remove item from cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipment_id'])) {
    $equipment_id = $_POST['equipment_id'];

    // Remove the item by filtering it out of the cart array
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($equipment_id) {
        return $item['equipment_id'] != $equipment_id;
    });

    // Reindex array to fix any gaps in the array
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

// Clear the entire cart
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch cart items from the session (safe check to ensure it's an array)
$cart_items = $_SESSION['cart'];

// Calculate the total price
$total_price = 0;
foreach ($cart_items as $cart_item) {
    $total_price += $cart_item['price'] * $cart_item['quantity'];
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
            background-color: rgba(34, 34, 34, 0.8);
            color: white;
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
        }

        /* Cart Page Styles */
        .cart-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.23);
            border-radius: 15px;
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
            background-color: rgba(255, 255, 255, 0.23);
            padding: 15px;
            border-radius: 10px;
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

        .remove-btn, .clear-cart-btn {
            background-color:rgb(0, 0, 0);
            color: white;
            padding: 5px 15px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
        }

        .remove-btn:hover, .clear-cart-btn:hover {
            background-color:rgb(0, 0, 0);
        }

        .cart-total {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
            color: #fff;
        }

        .clear-cart-container {
            margin-top: 20px;
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
            foreach ($cart_items as $cart_item) {
        ?>
            <div class="cart-item">
                <img src="<?php echo htmlspecialchars($cart_item['image']); ?>" alt="<?php echo htmlspecialchars($cart_item['name']); ?>">
                <div class="info">
                    <h3><?php echo htmlspecialchars($cart_item['name']); ?></h3>
                    <p class="price">₹<?php echo htmlspecialchars($cart_item['price']); ?> x <?php echo $cart_item['quantity']; ?></p>
                </div>
                <form method="POST">
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

        <!-- Clear Cart Button -->
        <div class="clear-cart-container">
            <?php if (count($cart_items) > 0) { ?>
                <form method="POST">
                    <button type="submit" name="clear_cart" class="clear-cart-btn">Clear Cart</button>
                </form>
            <?php } ?>
        </div>
    </div>

</body>
</html>
