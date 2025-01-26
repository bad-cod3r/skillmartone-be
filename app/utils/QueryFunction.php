<?php
require_once __DIR__ . '/Pagination.php';
require_once __DIR__ . '/GenerateBaseURL.php';

function findAll($db_connect, $table, $params = []) {
    try {
        $pagination = pagination($table, $db_connect, $params);
        $stmt = mysqli_prepare($db_connect, $pagination['query']);
        mysqli_stmt_bind_param($stmt, "ii", ...$pagination['params']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
       
        $baseUrl = generateBaseUrl();
        $records = [];
        while ($row = mysqli_fetch_assoc($result)) {
            if (isset($row['image']) && $row['image'] !== '') {
                $row['image'] = $baseUrl . $row['image'];
            } else {
                $row['image'] = NULL;
            }
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
            $baseUrl = generateBaseUrl();
            if (isset($record['image']) && $record['image'] !== '') {
                $record['image'] = $baseUrl . $record['image'];
            } else {
                $record['image'] = NULL;
            }
            
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
            
            $baseUrl = generateBaseUrl();
            $selectQuery = "SELECT * FROM $table WHERE id = ?";
            $selectStmt = mysqli_prepare($db_connect, $selectQuery);
            mysqli_stmt_bind_param($selectStmt, 'i', $id);
            mysqli_stmt_execute($selectStmt);
            $result = mysqli_stmt_get_result($selectStmt);
            $newRecord = mysqli_fetch_assoc($result);
            
            if (isset($newRecord['image'])) {
                $newRecord['image'] = $baseUrl . $newRecord['image'];
            }
 
            sendResponse(
                success: true,
                code: 201,
                message: "Data created successfully",
                data: $newRecord
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

function destroyWithImage($db_connect, $table, $id, $directory) {
    try {
        $query = "SELECT image FROM $table WHERE id = ?";
        $stmt = mysqli_prepare($db_connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        if ($data && $data['image']) {
            $imagePath = __DIR__ . '/../../public' . $data['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        destroy($db_connect, $table, $id);
        
    } catch (Exception $e) {
        sendResponse(
            success: false,
            code: 500,
            message: "Failed to delete record: " . $e->getMessage()
        );
    }
}

function checkExists($db_connect, $table, $id) {
    try {
        $query = "SELECT id FROM $table WHERE id = ? AND is_active = 1";
        $stmt = mysqli_prepare($db_connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    } catch (Exception $e) {
        return false;
    }
}