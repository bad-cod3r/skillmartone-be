<?php
function sendResponse($success = true, $code = 200, $message = '', $page_data = null, $page_info = null) {
    $response = [
        'meta' => [
            'success' => $success,
            'code' => $code,
            'message' => $message
        ],
        'data' => [
            'page_data' => $page_data ?? new stdClass(),
            'page_info' => $page_info ?? new stdClass()
        ]
    ];

    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode($response);
    exit;
}