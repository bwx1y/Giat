<?php

class migration_20261022_000002_create_events_table
{
    public function up(PDO $pdo): void
    {
        $pdo->exec("
            DO $$
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'event_status') THEN
                    CREATE TYPE event_status AS ENUM ('Not started', 'in progress', 'done');
                END IF;
            END
            $$;

            CREATE TABLE IF NOT EXISTS events (
                id          SERIAL PRIMARY KEY,
                title       VARCHAR(255) NOT NULL,
                description TEXT,
                location    VARCHAR(255),
                start_date  TIMESTAMP,
                end_date    TIMESTAMP,
                status      event_status DEFAULT 'Not started',
                created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
                deleted_at  TIMESTAMP    DEFAULT NULL
            );
        ");
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("
            DROP TABLE IF EXISTS events CASCADE;
            DROP TYPE IF EXISTS event_status CASCADE;
        ");
    }
}
