<?php

namespace core;

use config\Database;
use Exception;
use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->db = Database::getConnection();

        if (!isset($this->table)) {
            throw new Exception("The \$table property has not been defined in the class." . static::class);
        }
    }

    /**
     * Fetch all records from the table.
     */
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    /**
     * Find a single record by primary key (ID).
     */
    public function findById(mixed $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Saves new data to the database and returns the newly created entity.
     *
     * @param array<string, mixed> $data Column => value pair data
     * @return array|object|null Returns the newly created entity, or null on failure
     */
    public function create(array $data): array|object|null
    {
        if (empty($data)) {
            return null;
        }

        $columns = array_keys($data);

        $escapedColumns = array_map(fn($col) => "\"{$col}\"", $columns);
        $columnClause = implode(', ', $escapedColumns);

        $placeholders = array_map(fn($col) => ":{$col}", $columns);
        $placeholderClause = implode(', ', $placeholders);

        $params = [];
        foreach ($data as $key => $value) {
            $params[":{$key}"] = $value;
        }

        $sql = "INSERT INTO \"{$this->table}\" ({$columnClause}) VALUES ({$placeholderClause}) RETURNING *";

        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute($params);

        if (!$success) {
            return null;
        }

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Updates data based on the primary key and returns the updated entity.
     *
     * @param int|string $id ID of the record to be updated
     * @param array<string, mixed> $data Column => value pairs
     * @return array|object|null Returns the updated entity, or null on failure
     */
    public function update(int|string $id, array$data): array|object|null
    {
        if (empty($data)) {
            return $this->findById($id);
        }

        $fields = [];
        $params = [':id' =>$id];

        foreach ($data as $key =>$value) {
            $fields[] = "\"{$key}\" = :{$key}";
            $params[":{$key}"] = $value;
        }

        $setClause = implode(', ', $fields);

        $sql = "UPDATE \"{$this->table}\" SET {$setClause} WHERE \"{$this->primaryKey}\" = :id RETURNING *";

        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute($params);

        if (!$success) {
            return null;
        }

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Delete a record by ID.
     */
    public function delete(mixed $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }
}