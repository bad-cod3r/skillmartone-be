<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/Response.php';
require_once __DIR__ . '/../../utils/QueryFunction.php';
require_once __DIR__ . '/../../utils/UploadImage.php';

function getAllProducts($db_connect)
{
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

  findAll($db_connect, 'product', $params);
}

function getProduct($db_connect, $id)
{
  findById($db_connect, 'product', $id);
}

function createProduct($db_connect)
{
  $data = $_POST;
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

  if (!isset($data['category_id']) || empty($data['category_id'])) {
    sendResponse(
      success: false,
      code: 400,
      message: 'Category is required'
    );
    return;
  }

  if (!checkExists($db_connect, 'category', $data['category_id'])) {
    sendResponse(
      success: false,
      code: 400,
      message: 'Category not found or inactive'
    );
    return;
  }

  if (!isset($data['price']) || empty($data['price'])) {
    sendResponse(
      success: false,
      code: 400,
      message: 'Price is required'
    );
    return;
  }

  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload = uploadImage($_FILES['image'], 'products');
    if ($upload['success']) {
      $data['image'] = $upload['path'];
    }
  }

  if (!isset($data['outlet_id']) || empty($data['outlet_id'])) {
    sendResponse(
      success: false,
      code: 400,
      message: 'Outlet is required'
    );
    return;
  }

  if (!checkExists($db_connect, 'outlet', $data['outlet_id'])) {
    sendResponse(
      success: false,
      code: 400,
      message: 'Outlet not found or inactive'
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

  create($db_connect, 'product', $data);
}

function updateProduct($db_connect, $id)
{
  $data = $_POST;
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

  if (!isset($data['category_id']) || empty($data['category_id'])) {
    sendResponse(
      success: false,
      code: 400,
      message: 'Category is required'
    );
    return;
  }

  if (!checkExists($db_connect, 'category', $data['category_id'])) {
    sendResponse(
      success: false,
      code: 400,
      message: 'Category not found or inactive'
    );
    return;
  }

  if (!isset($data['price']) || empty($data['price'])) {
    sendResponse(
      success: false,
      code: 400,
      message: 'Price is required'
    );
    return;
  }

  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload = uploadImage($_FILES['image'], 'products');
    if ($upload['success']) {
      $data['image'] = $upload['path'];
    }
  }

  if (!isset($data['outlet_id']) || empty($data['outlet_id'])) {
    sendResponse(
      success: false,
      code: 400,
      message: 'Outlet is required'
    );
    return;
  }

  if (!checkExists($db_connect, 'outlet', $data['outlet_id'])) {
    sendResponse(
      success: false,
      code: 400,
      message: 'Outlet not found or inactive'
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

  update($db_connect, 'product', $id, $data);
}

function deleteProduct($db_connect, $id)
{
  destroyWithImage($db_connect, 'product', $id, 'products');
}
