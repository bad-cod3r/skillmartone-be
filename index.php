<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/utils/Response.php';

$route = isset($_GET['route']) ? $_GET['route'] : '';

switch ($route) {
    case 'roles':
        require_once __DIR__ . '/app/api/role/RoleRoute.php';
        break;
    default:
        sendResponse(false, 404, 'Route not found');
}