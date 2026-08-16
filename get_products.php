<?php
include "db.php";

$category = isset($_GET['category']) ? $_GET['category'] : '';

if ($category === '') {
    echo "<p class='text-muted col-12'>No category specified.</p>";
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE category = ?");
mysqli_stmt_bind_param($stmt, "s", $category);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo "<p class='text-muted col-12'>No products found in this category yet.</p>";
    exit();
}

while ($row = mysqli_fetch_assoc($result)) {

    $price = floatval($row['price']);
    $stock = intval($row['stock'] ?? 1);
    $name  = htmlspecialchars($row['product_name']);
    $img   = htmlspecialchars($row['image']);
    $det   = htmlspecialchars($row['details']);
    $id    = intval($row['id']);

    if ($stock < 1) {
        $actionBtn = "<button class='btn btn-warning w-100' disabled>Out of Stock</button>";
    } else {
        $actionBtn = "
            <div class='d-flex gap-2'>
                <button class='btn btn-success flex-grow-1 add-to-cart'
                        data-id='{$id}'
                        data-name='{$name}'
                        data-price='{$price}'>
                    Add To Cart
                </button>
                <button class='btn btn-outline-danger add-to-wishlist'
                        data-id='{$id}'
                        data-name='{$name}'
                        data-price='{$price}'
                         title='Add to Wishlist'>
                    <i class='bi bi-heart-fill'></i>
                </button>
            </div>";
    }

   echo "
    <div class='col-md-3 col-6'>
        <div class='card product-card h-100'>
            <a href='product.php?id={$id}' style='text-decoration:none;'>
            <img src='uploads/{$img}'
                 class='card-img-top'
                 style='height:200px; object-fit:cover;'
                 alt='{$name}'>
            </a>
            <div class='card-body d-flex flex-column justify-content-between'>
                <div>
                    <h6><a href='product.php?id={$id}' style='text-decoration:none; color:inherit;'>{$name}</a></h6>
                    <p class='text-muted small'>{$det}</p>
                    <p class='small text-muted'>Stock: {$stock}</p>
                </div>
                <div>
                    <p class='fw-bold mb-2'>R" . number_format($price, 2) . "</p>
                    {$actionBtn}
                </div>
            </div>
        </div>
    </div>";
}
?>