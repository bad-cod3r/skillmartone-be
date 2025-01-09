<?php
function sendResponse($success = true, $code = 200, $message = '', $data = null, $page_info = null) {
    if ($page_info !== null) {
        $response = [
            'meta' => [
                'success' => $success,
                'code' => $code,
                'message' => $message,
            ],
            'data' => [
                'page_data' => $data ?? new stdClass(),
                'page_info' => $page_info ?? new stdClass()
            ]
        ];
    } else {
        $response = [
            'meta' => [
                'success' => $success,
                'code' => $code,
                'message' => $message,
            ],
            'data' => $data ?? new stdClass()
        ];
    }

    http_response_code($code);
    echo json_encode($response);
    exit;
}