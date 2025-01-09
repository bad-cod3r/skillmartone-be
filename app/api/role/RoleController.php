<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/Response.php';
require_once __DIR__ . '/../../utils/QueryFunction.php';

function getAllRoles($db_connect) {
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
    
    findAll($db_connect, 'role', $params);
}

function getRole($db_connect, $id) {
    findById($db_connect, 'role', $id);
}

function createRole($db_connect) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['name'])) {
        sendResponse(
            success: false, 
            code: 400, 
            message: 'Name is required'
        );
        return;
    }
    create($db_connect, 'role', $data);
}

function updateRole($db_connect, $id) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['name'])) {
        sendResponse(
            success: false, 
            code: 400, 
            message: 'Name and description are required'
        );
        return;
    }
    update($db_connect, 'role', $id, $data);
}

function deleteRole($db_connect, $id) {
    destroy($db_connect, 'role', $id);
}