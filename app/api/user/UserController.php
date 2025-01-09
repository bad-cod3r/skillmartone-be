<?php 
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/Response.php';
require_once __DIR__ . '/../../utils/QueryFunction.php';

function getAllUsers($db_connect) {
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
                'status' => $_GET['status'] ?? null
            ]
        ];
    }
    
    $params['filter'] = array_filter($params['filter'] ?? []);
    
    findAll($db_connect, 'user', $params);
}

function getUser($db_connect, $id) {
  findById($db_connect, 'user', $id);
}

function createUser($db_connect) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['username']) || empty($data['username'])) {
        sendResponse(
            success: false, 
            code: 400, 
            message: 'Username is required'
        );
        return;
    }

    if (!isset($data['password']) || empty($data['password'])) {
        sendResponse(
            success: false, 
            code: 400, 
            message: 'Password is required'
        );
        return;
    }

    if (!isset($data['role_id']) || empty($data['role_id'])) {
        sendResponse(
            success: false, 
            code: 400, 
            message: 'Role ID is required'
        );
        return;
    }

    if (isset($data['is_active']) && !in_array($data['is_active'], [0, 1], true)) {
        sendResponse(
            success: false,
            code: 400,
            message: 'Is active must be boolean (0 or 1)'
        );
        return;
    }

    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $data['is_active'] = $data['is_active'] ?? 1;
    
    create($db_connect, 'user', $data);
}

function updateUser($db_connect, $id) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data)) {
        sendResponse(
            success: false, 
            code: 400, 
            message: 'No data provided'
        );
        return;
    }

    if (isset($data['username']) && empty($data['username'])) {
        sendResponse(
            success: false, 
            code: 400, 
            message: 'Username cannot be empty'
        );
        return;
    }

    if (isset($data['is_active']) && !in_array($data['is_active'], [0, 1], true)) {
        sendResponse(
            success: false,
            code: 400,
            message: 'Is active must be boolean (0 or 1)'
        );
        return;
    }

    if (isset($data['password']) && !empty($data['password'])) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    } else {
        unset($data['password']);
    }

    if (isset($data['role_id']) && empty($data['role_id'])) {
        sendResponse(
            success: false, 
            code: 400, 
            message: 'Role ID cannot be empty'
        );
        return;
    }
    
    update($db_connect, 'user', $id, $data);
}

function deleteUser($db_connect, $id) {
  destroy($db_connect, 'user', $id);
}