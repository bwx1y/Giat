<?php

class migration_20261104_163420_vw_user_attendance_history {

    public function up(PDO $pdo): void {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE vw_user_attendance_history (...)";
         $pdo->exec("CREATE VIEW vw_user_attendance_history AS
SELECT 
    ue.user_id,
    e.title AS main_event,
    m.title AS sub_event,
    m.start_date AS raw_date,
    TO_CHAR(m.start_date, 'DD Mon YYYY') AS date,
    CASE 
        WHEN um.id IS NOT NULL THEN 'Present'
        ELSE 'Absent'
    END AS status,
    CASE 
        WHEN um.id IS NOT NULL THEN UPPER(um.attendance_method::text)
        ELSE '—'
    END AS method,
    CASE 
        WHEN um.id IS NOT NULL THEN '+3'
        ELSE '-1'
    END AS point
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
WHERE ue.deleted_at IS NULL");
    }

    public function down(PDO $pdo): void {
         $pdo->exec("DROP VIEW IF EXISTS vw_user_attendance_history");
    }
}
