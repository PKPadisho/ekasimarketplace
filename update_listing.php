<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: Login.php");
    exit();
}

$id      = intval($_POST['id']);
$name    = trim($_POST['product_name']);
$price   = floatval($_POST['price']);
$stock   = intval($_POST['stock']);
$details = trim($_POST['details']);
$userId  = $_SESSION['user_id']; // FIX: was $user_id (undefined variable)

// Handle optional new image upload
$imagePath = trim($_POST['old_image']);

if(isset($_FILES['product-image']) && $_FILES['product-image']['error'] === 0){
    $filename = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['product-image']['name']));
    if(move_uploaded_file($_FILES['product-image']['tmp_name'], "uploads/" . $filename)){
        $imagePath = $filename;
    }
}

// Use prepared statement (FIX: old code used raw string interpolation)
$stmt = mysqli_prepare($conn,
    "UPDATE products SET product_name=?, price=?, stock=?, details=?, image=?
     WHERE id=? AND user_id=?");
mysqli_stmt_bind_param($stmt, "sdissii", $name, $price, $stock, $details, $imagePath, $id, $userId);
mysqli_stmt_execute($stmt);

header("Location: index2.php");
exit();