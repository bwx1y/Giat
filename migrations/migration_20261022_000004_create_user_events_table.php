<?php

class migration_20261022_000004_create_user_events_table
{
    public function up(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS user_events (
                id         SERIAL PRIMARY KEY,
                user_id    INT NOT NULL REFERENCES users (id) ON DELETE CASCADE,
                event_id   INT NOT NULL REFERENCES events (id) ON DELETE CASCADE,
                role       user_role,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP DEFAULT NULL
            );

            CREATE UNIQUE INDEX IF NOT EXISTS idx_user_events_active
                ON user_events (user_id, event_id)
                WHERE deleted_at IS NULL;
        ");
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS user_events CASCADE;");
    }
}
