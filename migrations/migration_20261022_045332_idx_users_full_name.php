<?php

class migration_20261022_045332_idx_users_full_name {

    public function up(PDO $pdo): void {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE idx_users_full_name (...)";
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_user_full_name ON users USING gin (full_name gin_trgm_ops)");
    }

    public function down(PDO $pdo): void {
         $pdo->exec("DROP INDEX IF EXISTS idx_users_full_name");
    }
}
