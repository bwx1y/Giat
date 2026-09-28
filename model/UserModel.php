<?php

namespace model;

use core\Model;
use PDO;

class UserModel extends Model
{
    protected string $table = "users";

    public function findByUsername(string $username): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}