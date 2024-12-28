<?php
require_once('../conn.php');
$query = "SELECT * FROM order";
$sql   = mysqli_query($db_connect, $query);

if ($sql) {
    $result = array();
    while ($row = mysqli_fetch_array($sql)) {
        array_push($result, array(
            'user_id' => $row['user_id'],
            'order_number' => $row['order_number']
        ));
    };
    echo json_encode(array('order' => $result));
}
