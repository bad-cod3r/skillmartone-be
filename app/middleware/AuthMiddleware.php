<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../utils/Response.php';

function authMiddleware($db_connect) {
    $headers = getallheaders();
    $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

    if (!$token) {
        sendResponse(
            success: false,
            code: 401,
            message: 'No token provided'
        );
        exit;
    }

    if (!verifyJWT($token)) {
        sendResponse(
            success: false,
            code: 401,
            message: 'Invalid token'
        );
        exit;
    }
}