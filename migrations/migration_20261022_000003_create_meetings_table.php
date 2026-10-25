<?php

class migration_20261022_000003_create_meetings_table
{
    public function up(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS meetings (
                id         SERIAL PRIMARY KEY,
                event_id   INT          NOT NULL REFERENCES events (id) ON DELETE CASCADE,
                title      VARCHAR(255) NOT NULL,
                location   VARCHAR(255),
                start_date TIMESTAMP,
                end_date   TIMESTAMP,
                status     event_status DEFAULT 'Not started',
                created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP    DEFAULT NULL
            );
        ");
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS meetings CASCADE;");
    }
}
