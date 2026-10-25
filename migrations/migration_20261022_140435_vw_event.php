<?php

class migration_20261022_140435_vw_event
{

    public function up(PDO $pdo): void
    {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE vw_event (...)";
        $pdo->exec('CREATE OR REPLACE VIEW vw_event AS
SELECT events.id,
       events.title,
       events.status,
       events.description,
       events.location,
       events.start_date,
       events.end_date,
       count(meetings.*)    AS sub_event_count,
       count(user_events.*) AS member_count
FROM events
         JOIN user_events ON user_events.event_id = events.id
         JOIN meetings ON meetings.event_id = events.id
where events.deleted_at IS NULL
  AND meetings.deleted_at IS NULL
  AND user_events.deleted_at IS NULL
GROUP BY events.id
ORDER BY events.id DESC');
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP VIEW IF EXISTS vw_event");
    }
}
