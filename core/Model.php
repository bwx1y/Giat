<?php

namespace core;

use config\Database;
use Exception;
use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $table;

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
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    /**
     * Find a single record by primary key (ID).
     */
    public function find(mixed $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Delete a record by ID.
     */
    public function delete(mixed $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}