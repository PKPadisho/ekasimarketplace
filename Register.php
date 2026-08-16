<?php

include "db.php";

if(isset($_POST['register'])){

    $fullname = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

 
    $check = mysqli_prepare(
        $conn,
        "SELECT id FROM users WHERE email = ?"
    );

    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if(mysqli_stmt_num_rows($check) > 0){

        $error = "An account with this email already exists.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO users(fullname, email, phone, password)
             VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param($stmt, "ssss", $fullname, $email, $phone, $password);

        if(mysqli_stmt_execute($stmt)){
            header("Location: Login.php");
            exit();
        } else {
            $error = "Registration failed.";
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_stmt_close($check);
}

?>


<!DOCTYPE html>
<html>

<head>
   <title>Register</title>
   <link rel="stylesheet" href= "css3.css">
</head>

<body>

<div class = "card">

    <div class = "card-header">
         <h1>Create Account</h1>
	</div>

<div class = "card-body">
   <?php if(isset($error)): ?>
       <p style="color:red;text-align:center;">
        <?php echo htmlspecialchars($error); ?>
       </p>
   <?php endif; ?>
   
   <form method = "POST">
      <label for="fname">Full Name</label><br>
      <input class="form-control"
	         name="name"
             type="text"
             id="register-name"
             placeholder="Enter full name"><br><br>

       Email<br>
       <input class="form-control"
	          name="email"
              type="email"
              id="register-email"
              placeholder="example@gmail.com"><br><br>

       Phone Number<br>
       <input class="form-control"
	          name="phone"
              type="number"
              id="register-phone"><br><br>

       Password<br>
       <input class="form-control"
	          name="password"
              type="password"
              id="register-password"><br><br>
	    
		<button type="submit" name="register" class="btn">
                Register
        </button>
   </form>
</div>

<div class = "card-footer">
   <p>
     Already have an account?
     <a href = "Login.php">login</a>
   </p>
</div>


</body>
</html>