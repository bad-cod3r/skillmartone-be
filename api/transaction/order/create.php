<?php 
require_once('../conn.php');
$order_name = $_POST['order_name'];
$query = "INSERT INTO order(order_name) VALUES ('$order_name')";
$sql   = mysqli_query($db_connect, $query);

if ($sql){
    echo json_encode (array('message'=>'created!'));
}else{
    echo json_encode (array('message'=>'error!'));

}

?>