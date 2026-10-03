<?php

class migration_20261003_175554_index_users
{

    public function up(PDO $pdo): void
    {
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS create_at BIGINT NOT NULL DEFAULT (EXTRACT(EPOCH FROM now()) * 1000)::BIGINT");

        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_users_created_at_id ON users (create_at DESC, id DESC)");

        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_users_username ON users (username)");
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP INDEX IF EXISTS idx_users_created_at_id");
        $pdo->exec("DROP INDEX IF EXISTS idx_users_username");
        $pdo->exec("ALTER TABLE users DROP COLUMN IF EXISTS created_at");
    }
}
