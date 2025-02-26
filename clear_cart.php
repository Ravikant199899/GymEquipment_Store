<?php
session_start();
unset($_SESSION['cart']); // Clear cart
echo json_encode(["status" => "success"]);
exit;
?>
