<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
$current = basename($_SERVER['PHP_SELF']);
?>

<aside class="admin-sidebar">

    <div class="sidebar-brand">
        <h5>&#127760; eKasi Admin</h5>
        <small>Management Panel</small>
    </div>

    <nav class="sidebar-nav">

        <a href="admin_dashboard.php"
           class="<?= $current=='admin_dashboard.php' ? 'active' : '' ?>">
           &#128202; Dashboard
        </a>

        <a href="users.php"
           class="<?= $current=='users.php' ? 'active' : '' ?>">
           &#128100; Users
        </a>

        <a href="products.php"
           class="<?= $current=='products.php' ? 'active' : '' ?>">
           &#128230; Products
        </a>

        <a href="orders.php"
           class="<?= $current=='orders.php' ? 'active' : '' ?>">
           &#128196; Orders
        </a>

        <?php if(isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin'): ?>
        <a href="admins.php"
           class="<?= $current=='admins.php' ? 'active' : '' ?>">
           &#128272; Admins
        </a>
        <?php endif; ?>

    </nav>

    <div class="sidebar-footer">

        <div class="admin-name">Logged in as</div>

        <div class="admin-role">
            <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
        </div>

        <a href="admin_logout.php"
           class="btn btn-sm btn-outline-light w-100">
           Logout
        </a>

    </div>

</aside>