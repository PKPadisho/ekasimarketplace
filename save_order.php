<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])) exit();
if(!isset($_SESSION['pending_cart']) || empty($_SESSION['pending_cart'])) exit();

$buyerId = $_SESSION['user_id'];
$address = $_SESSION['pending_address'] ?? '';
$cart    = $_SESSION['pending_cart'];

foreach($cart as $item){
    $productId = intval($item['id']);   // ← use ID directly, not name
    $price     = floatval($item['price'] ?? 0);

    // Insert order
    $ins = mysqli_prepare($conn,
        "INSERT INTO orders (buyer_id, product_id, address, amount, payment_status)
         VALUES (?, ?, ?, ?, 'Paid')");
    mysqli_stmt_bind_param($ins, "iisd", $buyerId, $productId, $address, $price);
    mysqli_stmt_execute($ins);

    // Decrease stock 
    $upd = mysqli_prepare($conn,
        "UPDATE products SET stock = GREATEST(stock - 1, 0) WHERE id = ?");
    mysqli_stmt_bind_param($upd, "i", $productId);
    mysqli_stmt_execute($upd);
}

unset($_SESSION['pending_cart']);
unset($_SESSION['pending_address']);
unset($_SESSION['pending_total']);

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header("Location: my_orders.php");
    exit();
}

echo "OK";
?>