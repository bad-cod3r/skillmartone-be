<?php
require_once __DIR__ . '/ProductController.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

authMiddleware($db_connect);

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($action === 'show' && $id) {
            getProduct($db_connect, $id);
        } else {
            getAllProducts($db_connect);
        }
        break;

    case 'POST':
        if ($action === 'update') {
            if ($id) {
                updateProduct($db_connect, $id);
            } else {
                sendResponse(
                    success: false,
                    code: 400,
                    message: 'ID is required for update'
                );
            }
        } else {
            createProduct($db_connect);
        }
        break;

    case 'DELETE':
        if ($id) {
            deleteProduct($db_connect, $id);
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