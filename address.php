<?php

session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    exit("Not logged in");
}

$userId  = $_SESSION['user_id'];
$address = trim($_POST['address'] ?? '');

if($address === ''){
    exit("Address is required");
}

$stmt = mysqli_prepare($conn, "UPDATE users SET address = ? WHERE id = ?");

mysqli_stmt_bind_param($stmt, "si", $address, $userId);

if(mysqli_stmt_execute($stmt)){
    $_SESSION['address'] = $address;
    echo "success";
} else {
    echo "error";
}

mysqli_stmt_close($stmt);

?>