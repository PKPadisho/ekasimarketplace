<?php
session_start();
include "db.php"; 

if(isset($_POST['login'])){
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);

    if($user){
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email']    = $user['email'];
			$_SESSION['address']  = $user['address'] ?? '';
			$_SESSION['role']     = $user['role'] ?? 'user';
            header("Location: index2.php");
            exit();			
        } else {
            $error = "Wrong password. Please try again";
        }
    } else {
        $error = "No account found with that email";
    }

}
?>


<!DOCTYPE html>
<html>

<head>
   <meta charset="UTF-8">
   <title>Login - eKasi Marketplace</title>
   <link rel="stylesheet" href= "css3.css">
</head>

<body>

<div class = "card">
  <div class = "card-header"><h1>Login</h1></div>
   <div class = "card-body">
   
    <?php if(isset($error)): ?>
            <p style="color:red;text-align:center;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            Email<br>
            <input class="form-control" name="email" type="email"
                   placeholder="example@gmail.com" required><br><br>
            Password<br>
            <input class="form-control" name="password" type="password" required><br><br>
            <button type="submit" name="login" class="btn">Login</button>
        </form>

        <p style="text-align:center;margin-top:12px;">
            <a href="forgot_password.php">Forgot Password?</a>
        </p>
    </div>
    <div class="card-footer">
        <p>Don't have an account? <a href="Register.php">Register</a></p>
    </div>
</div>
</body>
</html>