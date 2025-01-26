<?php
function pagination($table, $db_connect, $params = []) {
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
    $sort = isset($params['sort']) ? mysqli_real_escape_string($db_connect, $params['sort']) : 'id';
    $order = isset($params['order']) ? strtoupper($params['order']) : 'DESC';
    $search = isset($params['search']) ? mysqli_real_escape_string($db_connect, $params['search']) : '';
    $searchableColumns = $params['searchableColumns'];
    $conditions = [];
    
    if (!empty($search)) {
        $searchConditions = [];
        foreach ($searchableColumns as $column) {
            $column = mysqli_real_escape_string($db_connect, $column);
            $searchConditions[] = "$column LIKE '%$search%'";
        }
        $conditions[] = "(" . implode(' OR ', $searchConditions) . ")";
    }
    
    if (!empty($params['filter']) && is_array($params['filter'])) {
        foreach ($params['filter'] as $key => $value) {
            $key = mysqli_real_escape_string($db_connect, $key);
            $value = mysqli_real_escape_string($db_connect, $value);
            $conditions[] = "$key = '$value'";
        }
    }
    
    $where_clause = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : "";
    $order_clause = "ORDER BY $sort $order";
    
    $offset = ($page - 1) * $limit;

    $count_query = "SELECT COUNT(*) as total FROM {$table} {$where_clause}";
    $count_result = mysqli_query($db_connect, $count_query);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    
    return [
        'query' => "SELECT * FROM {$table} {$where_clause} {$order_clause} LIMIT ? OFFSET ?",
        'params' => [$limit, $offset],
        'page_info' => [
            'current_page' => $page,
            'total_pages' => ceil($total_records / $limit),
            'total_records' => intval($total_records),
            'limit' => $limit
        ]
    ];
}