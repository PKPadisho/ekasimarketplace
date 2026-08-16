<?php
include "admin.php";
include "../db.php";

// Handle delete
if (isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: users.php?msg=deleted");
    exit();
}

// Handle edit save
if (isset($_POST['save_id'])) {
    $id      = intval($_POST['save_id']);
    $name    = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    mysqli_query($conn, "UPDATE users SET
        fullname='$name', email='$email', address='$address'
        WHERE id=$id");
    header("Location: users.php?msg=updated");
    exit();
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Users - eKasi Admin</title>
<link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="d-flex">
    <?php include "admin_sidebar.php"; ?>
    <main class="admin-main">
        <h1 class="page-title">&#128100; Users</h1>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?= $_GET['msg'] === 'deleted' ? 'User deleted.' : 'User updated.' ?>
            </div>
        <?php endif; ?>

        <div class="admin-table-wrap">
            <div class="table-header">
                <h6>All Users (<?= mysqli_num_rows($users) ?>)</h6>
            </div>
            <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($u = mysqli_fetch_assoc($users)): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['fullname']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td class="text-muted small">
                            <?= htmlspecialchars($u['address'] ?? 'Not set') ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1"
                                    onclick="openEdit(<?= htmlspecialchars(json_encode($u)) ?>)">
                                Edit
                            </button>
                            <form method="POST" style="display:inline"
                                  onsubmit="return confirm('Delete this user? This cannot be undone.')">
                                <input type="hidden" name="delete_id" value="<?= $u['id'] ?>">
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
                <h5 class="modal-title">Edit User</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="save_id" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="fullname" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="edit-email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" id="edit-address" class="form-control">
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
function openEdit(u) {
    document.getElementById('edit-id').value      = u.id;
    document.getElementById('edit-name').value    = u.fullname;
    document.getElementById('edit-email').value   = u.email;
    document.getElementById('edit-address').value = u.address ?? '';
    editModal.show();
}
</script>
</body>
</html>