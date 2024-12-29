<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/utils/Response.php';

$route = isset($_GET['route']) ? $_GET['route'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

switch ($route) {
    case 'roles':
        require_once __DIR__ . '/app/api/role/RoleRoute.php';
        break;
    default:
        echo json_encode([
            'meta' => [
                'success' => false,
                'code' => 404,
                'message' => 'Route not found'
            ],
            'data' => [
                'page_data' => new stdClass(),
                'page_info' => new stdClass()
            ]
        ]);
        break;
}