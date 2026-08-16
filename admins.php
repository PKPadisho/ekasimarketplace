<?php
include "admin.php";
requireSuperAdmin(); // only super admin can access
include "../db.php";

$error   = "";
$success = "";

// Handle create new admin
if (isset($_POST['create_admin'])) {
    $name     = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $role     = mysqli_real_escape_string($conn, $_POST['role']);

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT id FROM admins WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "An admin with that email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO admins (fullname, email, password, role)
                                 VALUES ('$name', '$email', '$hashed', '$role')");
            $success = "Admin account created successfully.";
        }
    }
}

// Handle delete
if (isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    // Prevent deleting yourself
    if ($id == $_SESSION['admin_id']) {
        $error = "You cannot delete your own account.";
    } else {
        mysqli_query($conn, "DELETE FROM admins WHERE id=$id");
        $success = "Admin deleted.";
    }
}

// Handle edit save
if (isset($_POST['save_id'])) {
    $id   = intval($_POST['save_id']);
    $name = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $email= mysqli_real_escape_string($conn, trim($_POST['email']));
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    mysqli_query($conn, "UPDATE admins SET fullname='$name', email='$email', role='$role' WHERE id=$id");

    // Update password only if provided
    if (!empty($_POST['new_password'])) {
        $hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE admins SET password='$hashed' WHERE id=$id");
    }
    $success = "Admin updated.";
}

$admins = mysqli_query($conn, "SELECT * FROM admins ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admins - eKasi Admin</title>
<link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="d-flex">
    <?php include "admin_sidebar.php"; ?>
    <main class="admin-main">
        <h1 class="page-title">&#128272; Admin Accounts</h1>

        <?php if($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Create New Admin -->
        <div class="admin-form-card mb-4">
            <h6>&#43; Create New Admin</h6>
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="fullname" class="form-control"
                               placeholder="Full Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="email" name="email" class="form-control"
                               placeholder="Email" required>
                    </div>
                    <div class="col-md-2">
                        <input type="password" name="password" class="form-control"
                               placeholder="Password" required>
                    </div>
                    <div class="col-md-2">
                        <select name="role" class="form-control">
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="create_admin"
                                class="btn btn-success w-100">
                            Create Admin
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Admins Table -->
        <div class="admin-table-wrap">
            <div class="table-header">
                <h6>All Admins (<?= mysqli_num_rows($admins) ?>)</h6>
            </div>
            <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($a = mysqli_fetch_assoc($admins)): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td>
                            <?= htmlspecialchars($a['fullname']) ?>
                            <?php if($a['id'] == $_SESSION['admin_id']): ?>
                                <span class="badge bg-success ms-1">You</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($a['email']) ?></td>
                        <td>
                            <span class="badge <?= $a['role'] === 'super_admin' ? 'bg-dark' : 'bg-primary' ?>">
                                <?= htmlspecialchars($a['role']) ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1"
                                    onclick="openEdit(<?= htmlspecialchars(json_encode($a)) ?>)">
                                Edit
                            </button>
                            <?php if($a['id'] != $_SESSION['admin_id']): ?>
                            <form method="POST" style="display:inline"
                                  onsubmit="return confirm('Delete this admin account?')">
                                <input type="hidden" name="delete_id" value="<?= $a['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        </div>
    </main>
</div>

<!-- Edit Admin Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Admin</h5>
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
                        <label class="form-label">Role</label>
                        <select name="role" id="edit-role" class="form-control">
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                        <input type="password" name="new_password" class="form-control"
                               placeholder="New password (optional)">
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
function openEdit(a) {
    document.getElementById('edit-id').value    = a.id;
    document.getElementById('edit-name').value  = a.fullname;
    document.getElementById('edit-email').value = a.email;
    document.getElementById('edit-role').value  = a.role;
    editModal.show();
}
</script>
</body>
</html>