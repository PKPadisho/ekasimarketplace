<?php
session_start();
include "db.php";

$message = "";

if(isset($_POST['step']) && $_POST['step'] === 'verify'){
    $email = $_POST['email'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($result) > 0){
        $_SESSION['reset_email'] = $email;
        $message = "email_found";
    } else {
        $message = "not_found";
    }
}

if(isset($_POST['step']) && $_POST['step'] === 'reset'){
    if(!isset($_SESSION['reset_email'])){
        header("Location: forgot_password.php");
        exit();
    }
    $newPass  = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $email    = $_SESSION['reset_email'];
    $stmt     = mysqli_prepare($conn, "UPDATE users SET password=? WHERE email=?");
    mysqli_stmt_bind_param($stmt, "ss", $newPass, $email);
    mysqli_stmt_execute($stmt);
    unset($_SESSION['reset_email']);
    $message = "reset_done";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - eKasi Marketplace</title>
    <link rel="stylesheet" href="css3.css">
</head>
<body>
<div class="card">
    <div class="card-header"><h1>Reset Password</h1></div>
    <div class="card-body">

    <?php if($message === "reset_done"): ?>
        <p style="color:green;text-align:center;">Password updated! <a href="Login.php">Login now</a></p>

    <?php elseif($message === "not_found"): ?>
        <p style="color:red;text-align:center;">No account found with that email.</p>
        <!-- Show email form again -->
        <form method="POST">
            <input type="hidden" name="step" value="verify">
            Email<br>
            <input class="form-control" type="email" name="email" placeholder="example@gmail.com" required><br><br>
            <button type="submit" class="btn">Find Account</button>
        </form>

    <?php elseif($message === "email_found" || isset($_SESSION['reset_email'])): ?>
        <!-- Step 2: set new password -->
        <p style="color:green;text-align:center;">Account found! Enter your new password.</p>
        <form method="POST">
            <input type="hidden" name="step" value="reset">
            New Password<br>
            <input class="form-control" type="password" name="new_password"
                   placeholder="Enter new password" minlength="6" required><br><br>
            <button type="submit" class="btn">Reset Password</button>
        </form>

    <?php else: ?>
        <!-- Step 1: enter email -->
        <form method="POST">
            <input type="hidden" name="step" value="verify">
            Email<br>
            <input class="form-control" type="email" name="email"
                   placeholder="example@gmail.com" required><br><br>
            <button type="submit" class="btn">Find Account</button>
        </form>
    <?php endif; ?>

    </div>
    <div class="card-footer">
        <a href="Login.php">&#8592; Back to Login</a>
    </div>
</div>
</body>
</html>