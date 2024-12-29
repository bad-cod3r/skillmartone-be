<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Pagination.php';

function index($db_connect) {
    try {
        $pagination = pagination('role', $db_connect);
        
        $stmt = mysqli_prepare($db_connect, $pagination['query']);
        mysqli_stmt_bind_param($stmt, "ii", ...$pagination['params']);
        mysqli_stmt_execute($stmt);
        $sql = mysqli_stmt_get_result($stmt);

        $result = array();
        while ($row = mysqli_fetch_array($sql)) {
            array_push($result, array(
                'id' => $row['id'],
                'name' => $row['name'],
                'description' => $row['description'],
            ));
        }

        sendResponse(
            success: true,
            code: 200,
            message: 'Success fetch roles data',
            page_data: $result,
            page_info: $pagination['page_info']
        );
    } catch (Exception $e) {
        sendResponse(
            success: false,
            code: 500,
            message: 'Failed to fetch roles: ' . $e->getMessage()
        );
    }
}

function show($db_connect, $id) {
    try {
        $query = "SELECT * FROM role WHERE id = ?";
        $stmt = mysqli_prepare($db_connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $role = mysqli_fetch_assoc($result);

        if ($role) {
            sendResponse(
                success: true,
                code: 200,
                message: 'Success fetch role detail',
                page_data: $role
            );
        } else {
            sendResponse(
                success: false,
                code: 404,
                message: 'Role not found'
            );
        }
    } catch (Exception $e) {
        sendResponse(
            success: false,
            code: 500,
            message: 'Failed to fetch role: ' . $e->getMessage()
        );
    }
}

function store($db_connect) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || !isset($data['description'])) {
            sendResponse(
                success: false,
                code: 400,
                message: 'Name and description are required'
            );
            return;
        }

        $query = "INSERT INTO role (name, description) VALUES (?, ?)";
        $stmt = mysqli_prepare($db_connect, $query);
        mysqli_stmt_bind_param($stmt, "ss", $data['name'], $data['description']);
        
        if (mysqli_stmt_execute($stmt)) {
            $role_id = mysqli_insert_id($db_connect);
            sendResponse(
                success: true,
                code: 201,
                message: 'Role created successfully',
                page_data: ['id' => $role_id]
            );
        } else {
            throw new Exception(mysqli_error($db_connect));
        }
    } catch (Exception $e) {
        sendResponse(
            success: false,
            code: 500,
            message: 'Failed to create role: ' . $e->getMessage()
        );
    }
}

function update($db_connect, $id) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || !isset($data['description'])) {
            sendResponse(
                success: false,
                code: 400,
                message: 'Name and description are required'
            );
            return;
        }

        $check_query = "SELECT id FROM role WHERE id = ?";
        $check_stmt = mysqli_prepare($db_connect, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($result) === 0) {
            sendResponse(
                success: false,
                code: 404,
                message: 'Role not found'
            );
            return;
        }

        $query = "UPDATE role SET name = ?, description = ? WHERE id = ?";
        $stmt = mysqli_prepare($db_connect, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $data['name'], $data['description'], $id);
        
        if (mysqli_stmt_execute($stmt)) {
            sendResponse(
                success: true,
                code: 200,
                message: 'Role updated successfully'
            );
        } else {
            throw new Exception(mysqli_error($db_connect));
        }
    } catch (Exception $e) {
        sendResponse(
            success: false,
            code: 500,
            message: 'Failed to update role: ' . $e->getMessage()
        );
    }
}

function destroy($db_connect, $id) {
    try {
        $check_query = "SELECT id FROM role WHERE id = ?";
        $check_stmt = mysqli_prepare($db_connect, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($result) === 0) {
            sendResponse(
                success: false,
                code: 404,
                message: 'Role not found'
            );
            return;
        }

        $query = "DELETE FROM role WHERE id = ?";
        $stmt = mysqli_prepare($db_connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            sendResponse(
                success: true,
                code: 200,
                message: 'Role deleted successfully'
            );
        } else {
            throw new Exception(mysqli_error($db_connect));
        }
    } catch (Exception $e) {
        sendResponse(
            success: false,
            code: 500,
            message: 'Failed to delete role: ' . $e->getMessage()
        );
    }
}