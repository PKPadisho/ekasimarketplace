<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    exit("Please login");
}

$user_id = $_SESSION['user_id'];

// Get products with sales count joined from orders
$stmt = mysqli_prepare($conn,
    "SELECT p.*,
            COUNT(o.id) AS total_sales
     FROM products p
     LEFT JOIN orders o ON o.product_id = p.id
     WHERE p.user_id = ?
     GROUP BY p.id
     ORDER BY p.created_at DESC"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0){
    echo "<p class='text-muted'>No products listed yet.</p>";
    exit();
}

while($row = mysqli_fetch_assoc($result)){
    $stock      = intval($row['stock']);
    $sales      = intval($row['total_sales']);
    $stockColor = $stock === 0 ? 'danger' : ($stock <= 5 ? 'warning' : 'success');
?>

<div class="card mb-3 shadow-sm">
    <div class="card-body">

        <div class="d-flex gap-3 align-items-start">

            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>"
                 style="width:70px;height:70px;object-fit:cover;border-radius:8px;">

            <div class="flex-grow-1">
                <h6 class="mb-1"><?php echo htmlspecialchars($row['product_name']); ?></h6>
                <p class="text-muted small mb-2"><?php echo htmlspecialchars($row['category']); ?></p>

                <div class="d-flex gap-3 flex-wrap mb-2">

                    <span class="small">
                        <strong>Price:</strong> R<?php echo number_format($row['price'], 2); ?>
                    </span>

                    <span class="small badge bg-<?php echo $stockColor; ?>">
                        Stock: <?php echo $stock; ?>
                        <?php if($stock === 0) echo ' — Out of Stock'; ?>
                        <?php if($stock > 0 && $stock <= 5) echo ' — Low Stock'; ?>
                    </span>

                    <span class="small badge bg-primary">
                        &#128722; <?php echo $sales; ?> Sale<?php echo $sales !== 1 ? 's' : ''; ?>
                    </span>

                </div>

                <a href="edit_listing.php?id=<?php echo $row['id']; ?>"
                   class="btn btn-warning btn-sm">
                    Edit Listing
                </a>

            </div>
        </div>

    </div>
</div>

<?php } ?>