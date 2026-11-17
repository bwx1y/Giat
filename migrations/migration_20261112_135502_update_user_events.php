<?php

class migration_20261112_135502_update_user_events {

    public function up(PDO $pdo): void {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE update_user_events (...)";
         $pdo->exec("ALTER TABLE user_meetings ADD COLUMN IF NOT EXISTS code VARCHAR(255) DEFAULT NULL;");
    }

    public function down(PDO $pdo): void {
         $pdo->exec("ALTER TABLE user_meetings DROP COLUMN IF EXISTS code;");
    }
}
