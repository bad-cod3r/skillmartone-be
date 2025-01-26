<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/Response.php';
require_once __DIR__ . '/../../utils/QueryFunction.php';

function getAllCategories($db_connect) {
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

    findAll($db_connect, 'category', $params);
}

function getCategory($db_connect, $id) {
    findById($db_connect, 'category', $id);
}

function createCategory($db_connect) {
  $data = json_decode(file_get_contents('php://input'), true);
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

    create($db_connect, 'category', $data);
}

function updateCategory($db_connect, $id) {
  $data = json_decode(file_get_contents('php://input'), true);
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

    update($db_connect, 'category', $id, $data);
}

function deleteCategory($db_connect, $id) {
    destroy($db_connect, 'category', $id);
}