<?php

require_once __DIR__ . '/RoleController.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($action === 'show' && $id) {
            show($db_connect, $id);
        } else {
            index($db_connect);
        }
        break;

    case 'POST':
        store($db_connect);
        break;

    case 'PUT':
        if ($id) {
            update($db_connect, $id);
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
            destroy($db_connect, $id);
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