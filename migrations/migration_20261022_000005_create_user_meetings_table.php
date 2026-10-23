<?php

class migration_20261022_000005_create_user_meetings_table
{
    public function up(PDO $pdo): void
    {
        $pdo->exec("
            DO $$
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'attendance_method') THEN
                    CREATE TYPE attendance_method AS ENUM ('Qr', 'Manually');
                END IF;
            END
            $$;

            CREATE TABLE IF NOT EXISTS user_meetings (
                id                SERIAL PRIMARY KEY,
                user_id           INT               NOT NULL REFERENCES users (id) ON DELETE CASCADE,
                meeting_id        INT               NOT NULL REFERENCES meetings (id) ON DELETE CASCADE,
                attendance_method attendance_method NOT NULL,
                created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                deleted_at        TIMESTAMP DEFAULT NULL
            );

            CREATE UNIQUE INDEX IF NOT EXISTS idx_user_meetings_active
                ON user_meetings (user_id, meeting_id)
                WHERE deleted_at IS NULL;
        ");
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("
            DROP TABLE IF EXISTS user_meetings CASCADE;
            DROP TYPE IF EXISTS attendance_method CASCADE;
        ");
    }
}
