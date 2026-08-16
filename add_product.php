<?php

session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
	header("Location: Login.php");
	exit();
}	
	
$userId   = $_SESSION['user_id'];
$name     = $_POST['product-name'];
$category = $_POST['product-category'];
$price    = $_POST['product-price'];
$details  = $_POST['product-details'];
$stock    = intval($_POST['stock'] ?? 0);	
$image    = $_FILES['product-image']['name'];

$image = preg_replace('/[^a-zA-Z0-9._-]/', '_', $image); 

move_uploaded_file(
          $_FILES['product-image']['tmp_name'],
		  "uploads/".$image );

$stmt = mysqli_prepare($conn,"INSERT INTO products(user_id, product_name, category, price, details, image, stock)	 
		VALUES(?, ?, ?, ?, ?, ?, ?)"); 
mysqli_stmt_bind_param($stmt, "issdssi", $userId, $name, $category, $price, $details, $image, $stock);					   
mysqli_stmt_execute($stmt);

header("Location: index2.php");
exit();					   
						   
?>