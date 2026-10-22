<?php

$applicationDb = require_once __DIR__ . "/database.php";

/**
 * Dynamically builds and executes a prepared statement with support for
 * filtering, column selection, and pagination (LIMIT & OFFSET).
 *
 * @param string      $table   Name of the table or view to query.
 * @param array       $columns List of columns to select (default: ['*']).
 * @param array       $filters Associative array of filters, e.g.,
 *                             [['key' => 'search', 'query' => 'name ILIKE :search', 'value' => '%bud%']]
 * @param int|null    $size    Number of rows per page for pagination (optional).
 * @param int|null    $page    Current page number for offset calculation (optional).
 *
 * @return PDOStatement        Returns the executed Statement object ready for fetching.
 *
 * @throws PDOException        If a database execution error occurs.
 */
function queryBuilder(string $table, array $columns = ['*'], array $filters = [], ?int $size = null, ?int $page = null)
{
    global $applicationDb;

    $selectColumns = empty($columns) ? '*' : implode(', ', $columns);

    $sql = "SELECT $selectColumns FROM $table";

    $whereClauses = [];
    $queryParams = [];

    foreach ($filters as $filter) {
        if (!empty($filter['query']) && isset($filter['value']) && $filter['value'] !== '') {
            $whereClauses[] = $filter['query'];

            $paramKey = str_replace('.', '_', $filter['key']);
            $queryParams[$paramKey] = $filter['value'];
        }
    }

    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
    }

    if ($size !== null) {
        $sql .= " LIMIT :size";
        $queryParams['size'] = $size;

        if ($page !== null && $page > 0) {
            $offset = ($page - 1) * $size;
            $sql .= " OFFSET :offset";
            $queryParams['offset'] = $offset;
        }
    }

    $stmt = $applicationDb->prepare($sql);

    foreach ($queryParams as $key => $val) {
        if ($key === 'size' || $key === 'offset') {
            $stmt->bindValue(":$key", (int)$val, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(":$key", $val);
        }
    }

    return $stmt;
}