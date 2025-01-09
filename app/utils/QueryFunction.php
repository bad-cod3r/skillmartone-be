<?php
require_once __DIR__ . '/Pagination.php';

function findAll($db_connect, $table, $params = []) {
    try {
        $pagination = pagination($table, $db_connect, $params);
        
        $stmt = mysqli_prepare($db_connect, $pagination['query']);
        mysqli_stmt_bind_param($stmt, "ii", ...$pagination['params']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $records = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $records[] = $row;
        }
        
        sendResponse(
            success: true,
            code: 200,
            message: "Success fetch $table data",
            data: $records,
            page_info: $pagination['page_info']
        );
    } catch (Exception $e) {
        sendResponse(
            success: false,
            code: 500,
            message: "Failed to fetch $table: " . $e->getMessage()
        );
    }
}

function findById($db_connect, $table, $id) {
    try {
        $query = "SELECT * FROM $table WHERE id = ?";
        $stmt = mysqli_prepare($db_connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $record = mysqli_fetch_assoc($result);

        if ($record) {
            sendResponse(
                success: true,
                code: 200,
                message: "Success fetch $table detail",
                data: $record
            );
        } else {
            sendResponse(
                success: false,
                code: 404,
                message: "Record not found"
            );
        }
    } catch (Exception $e) {
        sendResponse(
            success: false,
            code: 500,
            message: "Failed to fetch $table: " . $e->getMessage()
        );
    }
}

function create($db_connect, $table, $data) {
    try {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $types = str_repeat('s', count($data));
        
        $query = "INSERT INTO $table ($columns) VALUES ($values)";
        $stmt = mysqli_prepare($db_connect, $query);
        mysqli_stmt_bind_param($stmt, $types, ...array_values($data));
        
        if (mysqli_stmt_execute($stmt)) {
            $id = mysqli_insert_id($db_connect);
            sendResponse(
                success: true,
                code: 201,
                message: "Data created successfully",
            );
        } else {
            throw new Exception(mysqli_error($db_connect));
        }
    } catch (Exception $e) {
        sendResponse(
            success: false,
            code: 500,
            message: "Failed to create record: " . $e->getMessage()
        );
    }
}

function update($db_connect, $table, $id, $data) {
    try {
        $check_query = "SELECT id FROM $table WHERE id = ?";
        $check_stmt = mysqli_prepare($db_connect, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($result) === 0) {
            sendResponse(
                success: false,
                code: 404,
                message: "Record not found"
            );
            return;
        }

        $set_clause = implode(' = ?, ', array_keys($data)) . ' = ?';
        $query = "UPDATE $table SET $set_clause WHERE id = ?";
        
        $stmt = mysqli_prepare($db_connect, $query);
        $types = str_repeat('s', count($data)) . 'i';
        $values = array_values($data);
        $values[] = $id;
        
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        
        if (mysqli_stmt_execute($stmt)) {
            sendResponse(
                success: true,
                code: 200,
                message: "Data updated successfully"
            );
        } else {
            throw new Exception(mysqli_error($db_connect));
        }
    } catch (Exception $e) {
        sendResponse(
            success: false,
            code: 500,
            message: "Failed to update record: " . $e->getMessage()
        );
    }
}

function destroy($db_connect, $table, $id) {
    try {
        $check_query = "SELECT id FROM $table WHERE id = ?";
        $check_stmt = mysqli_prepare($db_connect, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($result) === 0) {
            sendResponse(
                success: false,
                code: 404,
                message: "Record not found"
            );
            return;
        }

        $query = "DELETE FROM $table WHERE id = ?";
        $stmt = mysqli_prepare($db_connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            sendResponse(
                success: true,
                code: 200,
                message: "Data deleted successfully"
            );
        } else {
            throw new Exception(mysqli_error($db_connect));
        }
    } catch (Exception $e) {
        sendResponse(
            success: false,
            code: 500,
            message: "Failed to delete record: " . $e->getMessage()
        );
    }
}