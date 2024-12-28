<?php 
function pagination($table, $db_connect, $conditions = "") {
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
  $offset = ($page - 1) * $limit;

  $count_query = "SELECT COUNT(*) as total FROM {$table} {$conditions}";
  $count_result = mysqli_query($db_connect, $count_query);
  $total_records = mysqli_fetch_assoc($count_result)['total'];
  $total_pages = ceil($total_records / $limit);

  return [
      'query' => "SELECT * FROM {$table} {$conditions} LIMIT ? OFFSET ?",
      'params' => [$limit, $offset],
      'page_info' => [
          'current_page' => $page,
          'total_pages' => $total_pages,
          'total_records' => $total_records,
          'limit' => $limit
      ]
  ];
}
?>