<?php
include "admin.php";
include "../db.php";

$users    = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM users"))[0];
$products = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM products"))[0];
$orders   = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM orders"))[0];

// Recent orders
$recentOrders = mysqli_query($conn, "
    SELECT o.id, o.amount, o.payment_status, o.created_at,
           u.fullname, p.product_name
    FROM orders o
    LEFT JOIN users    u ON o.buyer_id   = u.id
    LEFT JOIN products p ON o.product_id = p.id
    ORDER BY o.id DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - eKasi Admin</title>
<link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="d-flex">
    <?php include "admin_sidebar.php"; ?>

    <main class="admin-main">
        <h1 class="page-title">&#128202; Dashboard</h1>

        <!-- Stat Cards -->
        <div class="dashboard-grid mb-4">

            <div class="stat-card">
                <div class="stat-icon" style="background:#eff6ff;">&#128100;</div>
                <div>
                    <h6>Total Users</h6>
                    <h2><?= $users ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#f0fdf4;">&#128230;</div>
                <div>
                    <h6>Total Products</h6>
                    <h2><?= $products ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#fefce8;">&#128196;</div>
                <div>
                    <h6>Total Orders</h6>
                    <h2><?= $orders ?></h2>
                </div>
            </div>

        </div>

        <!-- Recent Orders -->
        <div class="admin-table-wrap">
            <div class="table-header">
                <h6>&#128336; Recent Orders</h6>
                <a href="orders.php" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Buyer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($o = mysqli_fetch_assoc($recentOrders)): ?>
                    <tr>
                        <td><strong>#<?= $o['id'] ?></strong></td>
                        <td><?= htmlspecialchars($o['product_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($o['fullname'] ?? 'N/A') ?></td>
                        <td><strong>R<?= number_format($o['amount'], 2) ?></strong></td>
                        <td>
                            <?php
                            $badge = match(strtolower($o['payment_status'] ?? '')) {
                                'paid'      => 'bg-success',
                                'pending'   => 'bg-warning text-dark',
                                'cancelled' => 'bg-danger',
                                default     => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $badge ?>">
                                <?= htmlspecialchars($o['payment_status'] ?? 'N/A') ?>
                            </span>
                        </td>
                        <td class="text-muted">
                            <?= !empty($o['created_at']) ? date('d M Y', strtotime($o['created_at'])) : 'N/A' ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        </div>

    </main>
</div>
<script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>