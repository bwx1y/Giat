<?php

class migration_20261022_000001_create_users_table
{
    public function up(PDO $pdo): void
    {
        $pdo->exec("
            DO $$
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'user_role') THEN
                    CREATE TYPE user_role AS ENUM ('Admin', 'Member');
                END IF;
            END
            $$;

            CREATE TABLE IF NOT EXISTS users (
                id         SERIAL PRIMARY KEY,
                full_name  VARCHAR(255) NOT NULL,
                username   VARCHAR(100) NOT NULL,
                password   VARCHAR(255) NOT NULL,
                role       user_role DEFAULT 'Member',
                active     BOOLEAN   DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP DEFAULT NULL
            );

            CREATE UNIQUE INDEX IF NOT EXISTS idx_users_unique_username_active
                ON users (username)
                WHERE deleted_at IS NULL;
        ");
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("
            DROP TABLE IF EXISTS users CASCADE;
            DROP TYPE IF EXISTS user_role CASCADE;
        ");
    }
}
