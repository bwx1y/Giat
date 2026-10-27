<?php

class migration_20261022_140435_vw_event
{

    public function up(PDO $pdo): void
    {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE vw_event (...)";
        $pdo->exec('CREATE OR REPLACE VIEW vw_event AS
SELECT 
    events.id,
    events.title,
    events.status,
    events.description,
    events.location,
    events.start_date,
    events.end_date,
    COUNT(DISTINCT meetings.id)    AS sub_event_count,
    COUNT(DISTINCT user_events.id) AS member_count
FROM events
LEFT JOIN user_events ON user_events.event_id = events.id AND user_events.deleted_at IS NULL
LEFT JOIN meetings ON meetings.event_id = events.id AND meetings.deleted_at IS NULL
WHERE events.deleted_at IS NULL
GROUP BY events.id
ORDER BY events.id DESC');
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP VIEW IF EXISTS vw_event");
    }
}
