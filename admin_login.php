<?php
session_start();

if(isset($_SESSION['admin_id'])){
    header("Location: admin_dashboard.php");
    exit();
}

include "../db.php";

$error = "";

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM admins WHERE email = ?"
    );

    mysqli_stmt_bind_param($stmt,"s",$email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $admin = mysqli_fetch_assoc($result);

    if($admin && password_verify($password,$admin['password'])){

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['fullname'];
        $_SESSION['admin_role'] = $admin['role'];

        header("Location: admin_dashboard.php");
        exit();

    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Login</title>

<link rel="stylesheet"
href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">

<link rel="stylesheet" href="admin.css">

</head>

<body class="admin-login-body">

<div class="admin-login-card">

    <div class="admin-login-header">

        <h3>&#128272; Admin Panel</h3>
        <small>eKasi Marketplace</small>

    </div>

    <div class="admin-login-body-inner">

        <?php if($error): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">

                <label>Email</label>

                <input type="email"
                       name="email"
                       class="form-control"
                       required>

            </div>

            <div class="mb-3">

                <label>Password</label>

                <input type="password"
                       name="password"
                       class="form-control"
                       required>

            </div>

            <button type="submit"
                    name="login"
                    class="btn btn-success w-100">

                Login

            </button>

        </form>

    </div>

</div>

</body>
</html>