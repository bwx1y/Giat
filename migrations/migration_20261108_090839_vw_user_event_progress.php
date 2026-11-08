<?php

class migration_20261108_090839_vw_user_event_progress {

    public function up(PDO $pdo): void {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE vw_user_event_progress (...)";
         $pdo->exec("CREATE OR REPLACE VIEW vw_user_event_progress AS
SELECT
    ue.user_id,
    e.id AS event_id,
    e.title AS event_title,
    e.start_date AS event_start_date,
    e.end_date AS event_end_date,
    m.id AS meeting_id,
    m.title AS meeting_title,
    m.start_date AS meeting_start_date,
    m.end_date AS meeting_end_date,
    CASE
        WHEN um.id IS NOT NULL THEN 'Hadir'
        WHEN COALESCE(m.end_date, m.start_date) > NOW() THEN 'Upcoming'
        ELSE 'Tidak Hadir'
    END AS status_kehadiran,
    CASE
        WHEN um.id IS NOT NULL THEN 3
        WHEN COALESCE(m.end_date, m.start_date) > NOW() THEN 0
        ELSE -1
    END AS point_obtained
FROM user_events ue
JOIN events e
    ON e.id = ue.event_id
   AND e.deleted_at IS NULL
JOIN meetings m
    ON m.event_id = e.id
   AND m.deleted_at IS NULL
LEFT JOIN user_meetings um
    ON um.meeting_id = m.id
   AND um.user_id = ue.user_id
   AND um.deleted_at IS NULL
WHERE ue.deleted_at IS NULL;");
    }

    public function down(PDO $pdo): void {
         $pdo->exec("DROP VIEW IF EXISTS vw_user_event_progress");
    }
}
