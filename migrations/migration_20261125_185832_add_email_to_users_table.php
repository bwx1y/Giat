<?php

class migration_20261125_185832_add_email_to_users_table {

    public function up (PDO $pdo) : void {
        $pdo->exec("
            ALTER TABLE users
                ADD COLUMN IF NOT EXISTS email VARCHAR(255) DEFAULT NULL;
                
            UPDATE users
                SET email = username || '@giat.test'
                WHERE email is NULL;
            
            CREATE UNIQUE INDEX IF NOT EXISTS idx_users_unique_email_active 
                ON users (email)
                WHERE deleted_at IS NULL AND email IS NOT NULL;
        ");
    }
    public function down (PDO $pdo): void {
        $pdo->exec("
            DROP INDEX IF EXISTS idx_users_unique_email_active;
            ALTER TABLE users DROP COLUMN IF EXISTS email;
        
        ");
    }
}
