<?php
require_once('../../../config/database.php');
require_once('../../../utils/pagination.php');

$pagination = pagination('role', $db_connect);

$stmt = mysqli_prepare($db_connect, $pagination['query']);
mysqli_stmt_bind_param($stmt, "ii", ...$pagination['params']);
mysqli_stmt_execute($stmt);
$sql = mysqli_stmt_get_result($stmt);

if ($sql) {
    $result = array();
    while ($row = mysqli_fetch_array($sql)) {
        array_push($result, array(
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
        ));
    }

    $response = [
        'meta' => [
            'success' => true,
            'code' => 200,
            'message' => 'Success fetch roles data'
        ],
        'data' => [
            'page_data' => $result,
            'page_info' => $pagination['page_info']
        ]
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    $response = [
        'meta' => [
            'success' => false,
            'code' => 500,
            'message' => 'Failed to fetch roles data'
        ],
        'data' => [
            'page_data' => new stdClass(),
            'page_info' => new stdClass()
        ]
    ];
    
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode($response);
}

mysqli_close($db_connect);
