<?php

global $applicationDb;
if (!$applicationDb instanceof PDO) {
    $applicationDb = require_once __DIR__ . "/database.php";
}

/**
 * Dynamically builds and executes a prepared statement with support for
 * filtering, column selection, joins, and pagination (LIMIT & OFFSET).
 *
 * @param string       $table   Name of the table or view to query.
 * @param array        $columns List of columns to select (default: ['*']).
 * @param array        $filters Associative array of filters, e.g.,
 *                              [['key' => 'search', 'query' => 'name ILIKE :search', 'value' => '%bud%']]
 * @param int|null     $size    Number of rows per page for pagination (alias for limit).
 * @param int|null     $page    Current page number for offset calculation (optional).
 * @param array|string $joins   JOIN clauses, as string or array of strings / definitions.
 * @param int|null     $limit   Row limit (e.g. 1 for get one). Overrides $size if provided.
 *
 * @return PDOStatement        Returns the executed Statement object ready for fetching.
 *
 * @throws PDOException        If a database execution error occurs.
 */
function queryBuilder(
    string $table,
    array $columns = ['*'],
    array $filters = [],
    ?int $size = null,
    ?int $page = null,
    array|string $joins = [],
    ?int $limit = null
): PDOStatement {
    global $applicationDb;

    $selectColumns = empty($columns) ? '*' : implode(', ', $columns);

    $sql = "SELECT $selectColumns FROM $table";

    if (!empty($joins)) {
        if (is_string($joins)) {
            $sql .= " $joins";
        } elseif (is_array($joins)) {
            foreach ($joins as $join) {
                if (is_string($join)) {
                    $sql .= " $join";
                } elseif (is_array($join) && !empty($join['table']) && !empty($join['on'])) {
                    $type = strtoupper($join['type'] ?? 'INNER');
                    $sql .= " $type JOIN {$join['table']} ON {$join['on']}";
                }
            }
        }
    }

    $whereClauses = [];
    $queryParams = [];

    foreach ($filters as $filter) {
        if (!empty($filter['query']) && isset($filter['value']) && $filter['value'] !== '') {
            $whereClauses[] = $filter['query'];

            if (str_contains($filter['query'], ':')) {
                $paramKey = str_replace('.', '_', $filter['key']);
                $queryParams[$paramKey] = $filter['value'];
            }
        }
    }

    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
    }

    $effectiveLimit = $limit ?? $size;

    if ($effectiveLimit !== null) {
        $sql .= " LIMIT :limit";
        $queryParams['limit'] = $effectiveLimit;

        if ($page !== null && $page > 0) {
            $offset = ($page - 1) * $effectiveLimit;
            $sql .= " OFFSET :offset";
            $queryParams['offset'] = $offset;
        }
    }

    $stmt = $applicationDb->prepare($sql);

    foreach ($queryParams as $key => $val) {
        if ($key === 'limit' || $key === 'offset') {
            $stmt->bindValue(":$key", (int)$val, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(":$key", $val);
        }
    }

    $stmt->execute();

    return $stmt;
}