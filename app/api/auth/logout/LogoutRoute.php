<?php

require_once __DIR__ . '/LogoutController.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        logout($db_connect);
        break;

    default:
        sendResponse(
            success: false,
            code: 405,
            message: 'Method not allowed'
        );
        break;
}