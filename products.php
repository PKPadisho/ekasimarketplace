<?php
include "admin.php";
include "../db.php";

// Handle delete
if (isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    header("Location: products.php?msg=deleted");
    exit();
}

// Handle edit save
if (isset($_POST['save_id'])) {
    $id       = intval($_POST['save_id']);
    $name     = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price    = floatval($_POST['price']);
    $stock    = intval($_POST['stock']);
    $details  = mysqli_real_escape_string($conn, $_POST['details']);
    mysqli_query($conn, "UPDATE products SET
        product_name='$name', category='$category',
        price=$price, stock=$stock, details='$details'
        WHERE id=$id");
    header("Location: products.php?msg=updated");
    exit();
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Products - eKasi Admin</title>
<link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="d-flex">
    <?php include "admin_sidebar.php"; ?>
    <main class="admin-main">
        <h1 class="page-title">&#128230; Products</h1>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?= $_GET['msg'] === 'deleted' ? 'Product deleted.' : 'Product updated.' ?>
            </div>
        <?php endif; ?>

        <div class="admin-table-wrap">
            <div class="table-header">
                <h6>All Products (<?= mysqli_num_rows($products) ?>)</h6>
            </div>
            <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($p = mysqli_fetch_assoc($products)): ?>
                    <tr id="row-<?= $p['id'] ?>">
                        <td><?= $p['id'] ?></td>
                        <td>
                            <img src="../uploads/<?= htmlspecialchars($p['image']) ?>"
                                 style="width:55px;height:55px;object-fit:cover;border-radius:8px;">
                        </td>
                        <td><?= htmlspecialchars($p['product_name']) ?></td>
                        <td><?= htmlspecialchars($p['category']) ?></td>
                        <td>R<?= number_format($p['price'], 2) ?></td>
                        <td><?= $p['stock'] ?></td>
                        <td class="text-muted small" style="max-width:150px;">
                            <?= htmlspecialchars(substr($p['details'], 0, 60)) ?>...
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1"
                                    onclick="openEdit(<?= htmlspecialchars(json_encode($p)) ?>)">
                                Edit
                            </button>
                            <form method="POST" style="display:inline"
                                  onsubmit="return confirm('Delete this product?')">
                                <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
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

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="save_id" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="product_name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" id="edit-category" class="form-control">
                            <option value="Electronics">Electronics</option>
                            <option value="Fashion">Fashion</option>
                            <option value="Personal Care">Personal Care</option>
                            <option value="Home & Lifestyle">Home &amp; Lifestyle</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (R)</label>
                        <input type="number" step="0.01" name="price" id="edit-price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" name="stock" id="edit-stock" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Details</label>
                        <textarea name="details" id="edit-details" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script>
const editModal = new bootstrap.Modal(document.getElementById('editModal'));

function openEdit(p) {
    document.getElementById('edit-id').value       = p.id;
    document.getElementById('edit-name').value     = p.product_name;
    document.getElementById('edit-category').value = p.category;
    document.getElementById('edit-price').value    = p.price;
    document.getElementById('edit-stock').value    = p.stock;
    document.getElementById('edit-details').value  = p.details;
    editModal.show();
}
</script>
</body>
</html>