<?php

require_once __DIR__ . '/AuthController.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        loginCustomer($db_connect);
        break;

    default:
        sendResponse(
            success: false,
            code: 405,
            message: 'Method not allowed'
        );
        break;
}