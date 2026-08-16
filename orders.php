<?php
include "admin.php";
include "../db.php";

// Handle status update
if (isset($_POST['update_id'])) {
    $id     = intval($_POST['update_id']);
    $status = mysqli_real_escape_string($conn, $_POST['payment_status']);
    mysqli_query($conn, "UPDATE orders SET payment_status='$status' WHERE id=$id");
    header("Location: orders.php?msg=updated");
    exit();
}

// Handle delete
if (isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    mysqli_query($conn, "DELETE FROM orders WHERE id=$id");
    header("Location: orders.php?msg=deleted");
    exit();
}

$orders = mysqli_query($conn, "
    SELECT o.*, 
           u.fullname AS buyer_name, u.email AS buyer_email,
           p.product_name, p.image, p.category
    FROM orders o
    LEFT JOIN users    u ON o.buyer_id   = u.id
    LEFT JOIN products p ON o.product_id = p.id
    ORDER BY o.id DESC
");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Orders - eKasi Admin</title>
<link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="d-flex">
    <?php include "admin_sidebar.php"; ?>
    <main class="admin-main">
        <h1 class="page-title">&#128196; Orders</h1>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?= $_GET['msg'] === 'deleted' ? 'Order deleted.' : 'Order status updated.' ?>
            </div>
        <?php endif; ?>

        <div class="admin-table-wrap">
            <div class="table-header">
                <h6>All Orders (<?= mysqli_num_rows($orders) ?>)</h6>
            </div>
            <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Buyer</th>
                        <th>Amount</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($o = mysqli_fetch_assoc($orders)): ?>
                    <tr>
                        <td><?= $o['id'] ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if(!empty($o['image'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($o['image']) ?>"
                                         style="width:45px;height:45px;object-fit:cover;border-radius:6px;">
                                <?php endif; ?>
                                <div>
                                    <div class="fw-semibold small"><?= htmlspecialchars($o['product_name'] ?? 'N/A') ?></div>
                                    <div class="text-muted" style="font-size:0.75rem"><?= htmlspecialchars($o['category'] ?? '') ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-semibold"><?= htmlspecialchars($o['buyer_name'] ?? 'N/A') ?></div>
                            <div class="text-muted" style="font-size:0.75rem"><?= htmlspecialchars($o['buyer_email'] ?? '') ?></div>
                        </td>
                        <td><strong>R<?= number_format($o['amount'], 2) ?></strong></td>
                        <td class="small text-muted" style="max-width:120px;">
                            <?= htmlspecialchars($o['address'] ?? 'N/A') ?>
                        </td>
                        <td>
                            <?php
                            $status = $o['payment_status'];
                            $badge = match(strtolower($status)) {
                                'paid'      => 'bg-success',
                                'pending'   => 'bg-warning text-dark',
                                'cancelled' => 'bg-danger',
                                default     => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $badge ?>"><?= htmlspecialchars($status) ?></span>
                        </td>
                        <td class="small text-muted">
                            <?= !empty($o['created_at']) ? date('d M Y', strtotime($o['created_at'])) : 'N/A' ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1"
                                    onclick="openEdit(<?= $o['id'] ?>, '<?= htmlspecialchars($o['payment_status']) ?>')">
                                Edit
                            </button>
                            <form method="POST" style="display:inline"
                                  onsubmit="return confirm('Delete this order?')">
                                <input type="hidden" name="delete_id" value="<?= $o['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        </div>
    </main>
</div>

<!-- Edit Status Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="update_id" id="edit-id">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" id="edit-status" class="form-control">
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Refunded">Refunded</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script>
const editModal = new bootstrap.Modal(document.getElementById('editModal'));
function openEdit(id, status) {
    document.getElementById('edit-id').value     = id;
    document.getElementById('edit-status').value = status;
    editModal.show();
}
</script>
</body>
</html>