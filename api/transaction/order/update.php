<?php
require_once('../conn.php');
parse_str(file_get_contents('php://input'), $value);
$user_id = $value['user_id'];
$order_number = $value['order_number'];
$query = "UPDATE order SET order_number ='$order_number' WHERE user_id ='$user_id'";
$sql   = mysqli_query($db_connect, $query);

if ($sql) {
    echo json_encode(array('message' => 'updated!'));
} else {
    echo json_encode(array('message' => 'error!'));
}
