<?php
session_start();

$input = json_decode(file_get_contents("php://input"), true);

if(isset($input['cart']) && isset($input['address']) && isset($input['total'])){
    $_SESSION['pending_cart']    = $input['cart'];
    $_SESSION['pending_address'] = $input['address'];
    $_SESSION['pending_total']   = floatval($input['total']);
    echo json_encode(['status' => 'ok']);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Missing data']);
}
?>