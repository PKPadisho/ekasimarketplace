<?php
// Include at the top of every admin page

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

function isSuperAdmin(){
    return isset($_SESSION['admin_role']) &&
           $_SESSION['admin_role'] === 'super_admin';
}

function requireSuperAdmin(){
    if(!isSuperAdmin()){
        header("Location: dashboard.php");
        exit();
    }
}
?>