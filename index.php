<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/api/config/database.php';
require_once __DIR__ . '/api/utils/Response.php';

$route = isset($_GET['route']) ? $_GET['route'] : '';

switch ($route) {
    case 'roles':
        require_once __DIR__ . '/api/routes/role.php';
        break;
    default:
        sendResponse(false, 404, 'Route not found');
}