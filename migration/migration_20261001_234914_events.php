<?php

class migration_20261001_234914_events {
    
    public function up(PDO $pdo): void {
        $pdo->exec("CREATE TABLE IF NOT EXISTS events (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    location VARCHAR(255),
    start_date TIMESTAMP,
    end_date TIMESTAMP,
    status event_status DEFAULT 'Not started',
    create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
    }

    public function down(PDO $pdo): void {
        $pdo->exec("DROP TABLE IF EXISTS events");
    }
}
