<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Handle removing the item from the cart
if (isset($_POST['equipment_id'])) {
    $equipment_id = $_POST['equipment_id'];

    // Check if the cart is initialized
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['equipment_id'] == $equipment_id) {
                unset($_SESSION['cart'][$key]);  // Remove item from the cart
                break;
            }
        }

        // Re-index the cart array to prevent gaps in the array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

header("Location: cart.php");  // Redirect back to the cart page
exit;
