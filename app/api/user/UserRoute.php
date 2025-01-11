<?php

require_once __DIR__ . '/UserController.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

authMiddleware($db_connect);

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($action === 'show' && $id) {
            getUser($db_connect, $id);
        } else {
            getAllUsers($db_connect);
        }
        break;

    case 'POST':
        createUser($db_connect);
        break;

    case 'PUT':
        if ($id) {
            updateUser($db_connect, $id);
        } else {
            sendResponse(
                success: false,
                code: 400,
                message: 'ID is required for update'
            );
        }
        break;

    case 'DELETE':
        if ($id) {
            deleteUser($db_connect, $id);
        } else {
            sendResponse(
                success: false,
                code: 400,
                message: 'ID is required for delete'
            );
        }
        break;

    default:
        sendResponse(
            success: false,
            code: 405,
            message: 'Method not allowed'
        );
        break;
}