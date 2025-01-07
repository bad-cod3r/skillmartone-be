<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/Response.php';
require_once __DIR__ . '/../../utils/Pagination.php';
require_once __DIR__ . '/../../utils/QueryFunction.php';

function getAllRoles($db_connect) {
    $page = $_GET['page'] ?? 1;
    $limit = $_GET['limit'] ?? 10;
    findAll($db_connect, 'role', $page, $limit);
}

function getRole($db_connect, $id) {
    findById($db_connect, 'role', $id);
}

function createRole($db_connect) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['name'])) {
        sendResponse(false, 400, 'Name is required');
        return;
    }
    create($db_connect, 'role', $data);
}

function updateRole($db_connect, $id) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['name']) || !isset($data['description'])) {
        sendResponse(false, 400, 'Name and description are required');
        return;
    }
    update($db_connect, 'role', $id, $data);
}

function deleteRole($db_connect, $id) {
    destroy($db_connect, 'role', $id);
}