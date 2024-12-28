<?php

require_once('conn.php');

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$sql = mysqli_query($db_connect, $query);
$data = mysqli_fetch_assoc($sql);

if($data){
    echo json_encode(
        array(
            "response" => true,
            "payload" => array(
                "username" => $data['username'],
            )
        )
    );
} else {
    echo json_encode(array(
        "response" => false,
        "payload" => null
    ));
}

header('Content-Type: application/json');

?>