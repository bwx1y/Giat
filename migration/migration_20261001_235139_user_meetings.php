<?php

class migration_20261001_235139_user_meetings {
    
    public function up(PDO $pdo): void {
        $pdo->exec("CREATE TABLE IF NOT EXISTS user_meetings (
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    meeting_id INT NOT NULL REFERENCES meetings(id) ON DELETE CASCADE,
    attendance_method attendance_method NOT NULL,
    create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, meeting_id)
)");
    }

    public function down(PDO $pdo): void {
        $pdo->exec("DROP TABLE IF EXISTS user_meetings");
    }
}
