<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: Login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// JOIN with products to get product name and image
$sql = "SELECT o.*, p.product_name, p.image, p.category
        FROM orders o
        LEFT JOIN products p ON o.product_id = p.id
        WHERE o.buyer_id = '$userId'
        ORDER BY o.id DESC";

$result = mysqli_query($conn, $sql);
$orderCount = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - eKasi Marketplace</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css4.css">
    <style>
        body { background-color: #f8f9fa; }

        .order-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .order-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .order-header .order-number {
            font-weight: 700;
            font-size: 1rem;
        }

        .order-header .order-dates {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .order-body {
            padding: 16px 20px;
        }

        .product-row {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-row:last-child {
            border-bottom: none;
        }

        .product-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
            background: #e9ecef;
        }

        .product-img-placeholder {
            width: 70px;
            height: 70px;
            border-radius: 8px;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .product-info h6 {
            margin-bottom: 2px;
            font-weight: 600;
        }

        .order-summary {
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 14px 20px;
        }

        .status-badge {
            font-size: 0.78rem;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state .icon {
            font-size: 4rem;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="index2.php">&#8592; eKasi Marketplace</a>
    </div>
</nav>

<div class="container mt-4 mb-5" style="max-width: 750px;">

    <h4 class="mb-4 fw-bold">My Orders</h4>

    <?php if($orderCount === 0): ?>

        <!-- Empty state -->
        <div class="order-card">
            <div class="empty-state">
                <div class="icon">&#128230;</div>
                <h5 class="fw-bold mb-2">No orders available</h5>
                <p class="text-muted mb-4">
                    You haven't placed any orders yet.<br>
                    Browse our products and find something you love!
                </p>
                <a href="index2.php" class="btn btn-success px-4">
                    Continue Shopping
                </a>
            </div>
        </div>

    <?php else: ?>

        <?php while($row = mysqli_fetch_assoc($result)): ?>

        <div class="order-card">

            <!-- Order Header -->
            <div class="order-header">
                <div>
                    <div class="order-number">Order #<?php echo $row['id']; ?></div>
                    <div class="order-dates">
                        <?php if(!empty($row['created_at'])): ?>
                            ORDERED: <?php echo date('j M Y', strtotime($row['created_at'])); ?>
                            &nbsp;&middot;&nbsp;
                            PAID: <?php echo date('j M Y', strtotime($row['created_at'])); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <span class="status-badge bg-success text-white">
                    <?php echo htmlspecialchars($row['payment_status']); ?>
                </span>
            </div>

            <!-- Product Row -->
            <div class="order-body">
                <div class="product-row">

                    <?php if(!empty($row['image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>"
                             class="product-img"
                             alt="<?php echo htmlspecialchars($row['product_name'] ?? 'Product'); ?>">
                    <?php else: ?>
                        <div class="product-img-placeholder">&#128230;</div>
                    <?php endif; ?>

                    <div class="product-info flex-grow-1">
                        <h6><?php echo htmlspecialchars($row['product_name'] ?? 'Product'); ?></h6>
                        <p class="text-muted small mb-1">
                            <?php echo htmlspecialchars($row['category'] ?? ''); ?>
                        </p>
                        <p class="mb-0">
                            <strong>R<?php echo number_format($row['amount'], 2); ?></strong>
                            &nbsp;&middot;&nbsp;
                            <span class="text-muted small">Qty: 1</span>
                        </p>
                    </div>

                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">1 Item</span>
                    <span class="small">R<?php echo number_format($row['amount'], 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">Delivery</span>
                    <?php $delivery = $row['amount'] >= 1000 ? 0 : 90; ?>
                    <span class="small <?= $delivery == 0 ? 'text-success' : '' ?>">
                         <?= $delivery == 0 ? 'Free 🎉' : 'R90.00' ?>
                    </span>
                </div>
                <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-1">
                    <span>Order Total:</span>
                    <?php $delivery = $row['amount'] >= 1000 ? 0 : 90; ?>
                    <span>R<?php echo number_format($row['amount'] + $delivery, 2); ?></span>

                </div>

                <?php if(!empty($row['address'])): ?>
                <div class="mt-3 pt-2 border-top">
                    <p class="text-muted small mb-1"><strong>SHIPPING ADDRESS</strong></p>
                    <p class="small mb-0"><?php echo nl2br(htmlspecialchars($row['address'])); ?></p>
                </div>
                <?php endif; ?>

                <div class="mt-3 pt-2 border-top">
                    <p class="text-muted small mb-1"><strong>PAYMENT METHOD</strong></p>
                    <p class="small mb-0">&#128179; PayFast (Credit / Debit Card)</p>
                </div>
            </div>

        </div>

        <?php endwhile; ?>

        <!-- Continue shopping link -->
        <div class="text-center mt-3">
            <a href="index2.php" class="btn btn-outline-success">
                &#128722; Continue Shopping
            </a>
        </div>

    <?php endif; ?>

</div>

<script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>