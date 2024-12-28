<?php
require_once('../conn.php');
parse_str(file_get_contents('php://input'), $value);
$user_id = $value['user_id'];

$query = "DELETE FROM order WHERE user_id ='$user_id'";
$sql   = mysqli_query($db_connect, $query);

if ($sql) {
    echo json_encode(array('message' => 'deleted!'));
} else {
    echo json_encode(array('message' => 'error!'));
}
