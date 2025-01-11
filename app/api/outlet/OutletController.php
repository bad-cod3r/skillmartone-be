<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/Response.php';
require_once __DIR__ . '/../../utils/QueryFunction.php';
require_once __DIR__ . '/../../utils/UploadImage.php';

function getAllOutlets($db_connect) {
    $jsonBody = file_get_contents('php://input');
    if (!empty($jsonBody)) {
        $requestData = json_decode($jsonBody, true);
        
        $page = isset($requestData['page']) ? filter_var($requestData['page'], FILTER_VALIDATE_INT) : 1;
        $limit = isset($requestData['limit']) ? filter_var($requestData['limit'], FILTER_VALIDATE_INT) : 10;
        
        $params = [
            'page' => $page ?: 1,
            'limit' => $limit ?: 10,
            'sort' => in_array($requestData['sort'] ?? '', ['id', 'name', 'created_at']) ? $requestData['sort'] : 'id',
            'order' => in_array(strtoupper($requestData['order'] ?? ''), ['ASC', 'DESC']) ? strtoupper($requestData['order']) : 'DESC',
            'search' => strip_tags($requestData['search'] ?? ''),
            'filter' => array_filter($requestData['filter'] ?? [])
        ];
    } else {
        $params = [
            'page' => $_GET['page'] ?? 1,
            'limit' => $_GET['limit'] ?? 10,
            'sort' => $_GET['sort'] ?? 'id',
            'order' => $_GET['order'] ?? 'desc',
            'search' => $_GET['search'] ?? '',
            'filter' => [
                'is_active' => $_GET['is_active'] ?? null
            ]
        ];
    }
    
    $params['filter'] = array_filter($params['filter'] ?? []);
    
    findAll($db_connect, 'outlet', $params);
}

function getOutlet($db_connect, $id) {
    findById($db_connect, 'outlet', $id);
}

function createOutlet($db_connect) {
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

    $upload = uploadImage($_FILES['image'], 'outlet');
        if ($upload['success']) {
            $data['image'] = $upload['path'];
        }

    create($db_connect, 'outlet', $data);
}

function updateOutlet($db_connect, $id) {
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

    if (isset($_FILES['image'])) {
        $upload = uploadImage($_FILES['image'], 'outlet');
        if ($upload['success']) {
            $data['image'] = $upload['path'];
        }
    }

    update($db_connect, 'outlet', $id, $data);
}

function deleteOutlet($db_connect, $id) {
    destroy($db_connect, 'outlet', $id);
}