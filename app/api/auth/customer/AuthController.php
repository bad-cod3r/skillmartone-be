<?php
require_once __DIR__ . '/../../../utils/JWT.php';

function loginCustomer($db_connect) {
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

    try {
        $query = "SELECT u.*, r.name as role_name FROM user u 
                 JOIN role r ON u.role_id = r.id 
                 WHERE u.username = ? AND u.role_id = 3";
        $stmt = mysqli_prepare($db_connect, $query);
        mysqli_stmt_bind_param($stmt, "s", $data['username']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if (!$user) {
            sendResponse(
                success: false,
                code: 401,
                message: 'Invalid username or password'
            );
            return;
        }

        if (!$user['is_active']) {
            sendResponse(
                success: false,
                code: 401,
                message: 'User is not active'
            );
            return;
        }

        if (!password_verify($data['password'], $user['password'])) {
            sendResponse(
                success: false,
                code: 401,
                message: 'Invalid username or password'
            );
            return;
        }

        unset($user['password']);

        $payload = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role_id' => $user['role_id'],
            'role_name' => $user['role_name'],
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24)
        ];

        $accessToken = generateJWT($payload);
        
        $user['access_token'] = $accessToken;

        sendResponse(
            success: true,
            code: 200,
            message: 'Login successful',
            data: $user
        );

    } catch (Exception $e) {
        sendResponse(
            success: false,
            code: 500,
            message: 'Login failed: ' . $e->getMessage()
        );
    }
}