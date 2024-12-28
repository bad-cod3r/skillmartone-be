<?php
require_once('../../../config/database.php');

$role_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$role_id) {
    $response = [
        'meta' => [
            'success' => false,
            'code' => 400,
            'message' => 'Role ID is required'
        ],
        'data' => new stdClass()
    ];
    
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode($response);
    exit;
}

$query = "SELECT * FROM role WHERE id = ?";
$stmt = mysqli_prepare($db_connect, $query);
mysqli_stmt_bind_param($stmt, "i", $role_id);
mysqli_stmt_execute($stmt);
$sql = mysqli_stmt_get_result($stmt);

if ($sql) {
    $row = mysqli_fetch_array($sql);
    
    if ($row) {
        $result = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
        );

        $response = [
            'meta' => [
                'success' => true,
                'code' => 200,
                'message' => 'Success fetch role detail'
            ],
            'data' => $result
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        $response = [
            'meta' => [
                'success' => false,
                'code' => 404,
                'message' => 'Role not found'
            ],
            'data' => new stdClass()
        ];
        
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode($response);
    }
} else {
    $response = [
        'meta' => [
            'success' => false,
            'code' => 500,
            'message' => 'Failed to fetch role detail'
        ],
        'data' => new stdClass()
    ];
    
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode($response);
}

mysqli_close($db_connect);