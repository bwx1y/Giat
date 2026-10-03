<?php

class migration_20261001_234947_meetings {
    
    public function up(PDO $pdo): void {
        $pdo->exec("CREATE TABLE IF NOT EXISTS meetings (
    id SERIAL PRIMARY KEY,
    event_id INT NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    start_date TIMESTAMP,
    end_date TIMESTAMP,
    status event_status DEFAULT 'Not started'
)");
    }

    public function down(PDO $pdo): void {
        $pdo->exec("DROP TABLE IF EXISTS meetings");
    }
}
