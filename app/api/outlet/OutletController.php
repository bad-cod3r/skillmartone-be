<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/Response.php';
require_once __DIR__ . '/../../utils/QueryFunction.php';
require_once __DIR__ . '/../../utils/UploadImage.php';

function getAllOutlets($db_connect)
{
    $jsonBody = file_get_contents('php://input');
    if (!empty($jsonBody)) {
        $requestData = json_decode($jsonBody, true);
    } else {
        $requestData = $_GET;
    }

    $searchableColumns = [
        'name',
        'code',
    ];

    $params = [
        'page' => filter_var($requestData['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1,
        'limit' => filter_var($requestData['limit'] ?? 10, FILTER_VALIDATE_INT) ?: 10,
        'sort' => in_array($requestData['sort'] ?? '', ['id', 'name', 'created_at']) ? $requestData['sort'] : 'id',
        'order' => in_array(strtoupper($requestData['order'] ?? ''), ['ASC', 'DESC']) ? strtoupper($requestData['order']) : 'DESC',
        'search' => strip_tags($requestData['search'] ?? ''),
        'searchableColumns' => $searchableColumns,
        'filter' => array_filter($requestData['filter'] ?? [])
    ];

    findAll($db_connect, 'outlet', $params);
}

function getOutlet($db_connect, $id)
{
    findById($db_connect, 'outlet', $id);
}

function createOutlet($db_connect)
{
    $data = $_POST;    
    if (!isset($data['code']) || empty($data['code'])) {
        sendResponse(
            success: false,
            code: 400,
            message: 'Code is required'
        );
        return;
    }

    if (!isset($data['name']) || empty($data['name'])) {
        sendResponse(
            success: false,
            code: 400,
            message: 'Name is required'
        );
        return;
    }

    if (!isset($data['is_active']) || !in_array((int)$data['is_active'], [0, 1], true)) {
        sendResponse(
            success: false,
            code: 400,
            message: 'Status must be boolean (0 or 1)'
        );
        return;
    }

    $data['is_active'] = (int)$data['is_active'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['image'], 'outlets');
        if ($upload['success']) {
            $data['image'] = $upload['path'];
        }
    }

    create($db_connect, 'outlet', $data);
}

function updateOutlet($db_connect, $id)
{
    $data = $_POST;
    if (!isset($data['code']) || empty($data['code'])) {
        sendResponse(
            success: false,
            code: 400,
            message: 'Code is required'
        );
        return;
    }

    if (!isset($data['name']) || empty($data['name'])) {
        sendResponse(
            success: false,
            code: 400,
            message: 'Name is required'
        );
        return;
    }

    if (!isset($data['is_active']) || !in_array((int)$data['is_active'], [0, 1], true)) {
        sendResponse(
            success: false,
            code: 400,
            message: 'Status must be boolean (0 or 1)'
        );
        return;
    }

    $data['is_active'] = (int)$data['is_active'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['image'], 'outlets');
        if ($upload['success']) {
            $data['image'] = $upload['path'];
        }
    }

    update($db_connect, 'outlet', $id, $data);
}

function deleteOutlet($db_connect, $id)
{
    destroyWithImage($db_connect, 'outlet', $id, 'outlets');
}
