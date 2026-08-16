<?php 

header("Content-Type: application/json"); 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['admin_id'])) {
    echo json_encode(["success" => true]);
    exit();
}

include "../db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit();
}

$email    = trim($_POST['email']    ?? '');
$password =      $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Email and password are required."]);
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT * FROM admins WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$admin  = mysqli_fetch_assoc($result);

if ($admin && password_verify($password, $admin['password'])) {

 
    $_SESSION['admin_id']   = $admin['id'];
    $_SESSION['admin_name'] = $admin['fullname'];
    $_SESSION['admin_role'] = $admin['role'];  

    echo json_encode(["success" => true]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);
}

mysqli_stmt_close($stmt);
exit();
