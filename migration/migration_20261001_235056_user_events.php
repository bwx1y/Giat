<?php

class migration_20261001_235056_user_events {
    
    public function up(PDO $pdo): void {
        $pdo->exec("CREATE TABLE IF NOT EXISTS user_events (
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    event_id INT NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    role user_role,
    PRIMARY KEY (user_id, event_id)
)");
    }

    public function down(PDO $pdo): void {
        $pdo->exec("DROP TABLE IF EXISTS user_events");
    }
}
