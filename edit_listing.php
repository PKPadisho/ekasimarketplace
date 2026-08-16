<?php

session_start();
include "db.php";

if(!isset($_GET['id'])){
    die("Product ID missing");
}

$id = intval($_GET['id']);

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM products WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if(!$product){
    die("Product not found");
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Listing</title>

    <link rel="stylesheet"
          href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">

    <h3>Edit Listing</h3>

    <form action="update_listing.php"
          method="POST"
          enctype="multipart/form-data">

        <input type="hidden"
               name="id"
               value="<?php echo $product['id']; ?>">

        <input type="hidden"
               name="old_image"
               value="<?php echo htmlspecialchars($product['image']); ?>">

        <div class="mb-3">

            <label class="form-label">
                Current Image
            </label>

            <br>

            <img
                src="uploads/<?php echo htmlspecialchars($product['image']); ?>"
                class="img-fluid rounded mb-2"
                style="max-height:200px;">

        </div>

        <div class="mb-3">

            <label class="form-label">
                Image
            </label>

            <input type="file"
                   name="product-image"
                   class="form-control"
                   accept="image/*">

        </div>

        <div class="mb-3">

            <label class="form-label">
                Product Name
            </label>

            <input type="text"
                   name="product_name"
                   class="form-control"
                   value="<?php echo htmlspecialchars($product['product_name']); ?>">

        </div>

        <div class="mb-3">

            <label class="form-label">
                Price
            </label>

            <input type="number"
                   step="0.01"
                   name="price"
                   class="form-control"
                   value="<?php echo $product['price']; ?>">

        </div>

        <div class="mb-3">

            <label class="form-label">
                Stock
            </label>

            <input type="number"
                   name="stock"
                   class="form-control"
                   value="<?php echo $product['stock']; ?>">

        </div>

        <div class="mb-3">

            <label class="form-label">
                Description
            </label>

            <textarea name="details"
                      class="form-control"
                      rows="4"><?php echo htmlspecialchars($product['details']); ?></textarea>

        </div>

        <a href="index2.php"
           class="btn btn-success">
            Save Changes
        </a>

    </form>

</div>

</body>
</html>