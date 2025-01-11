<?php

require_once __DIR__ . '/CategoryController.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($action === 'show' && $id) {
            getCategory($db_connect, $id);
        } else {
            getAllCategories($db_connect);
        }
        break;

    case 'POST':
        createCategory($db_connect);
        break;

    case 'PUT':
        if ($id) {
            updateCategory($db_connect, $id);
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
            deleteCategory($db_connect, $id);
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