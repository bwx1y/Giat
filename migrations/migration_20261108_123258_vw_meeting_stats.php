<?php

class migration_20261108_123258_vw_meeting_stats {

    public function up(PDO $pdo): void {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE vw_meeting_stats (...)";
         $pdo->exec("CREATE OR REPLACE VIEW vw_meeting_stats AS
WITH meeting_members AS (
    SELECT 
        ue.user_id,
        m.id AS meeting_id,
        m.end_date
    FROM user_events ue
    JOIN meetings m 
        ON ue.event_id = m.event_id 
       AND m.deleted_at IS NULL
    JOIN users u 
        ON ue.user_id = u.id 
       AND u.deleted_at IS NULL
    WHERE ue.deleted_at IS NULL
),
attendance_summary AS (
    SELECT 
        mm.meeting_id,
        mm.user_id,
        CASE WHEN um.id IS NOT NULL THEN 1 ELSE 0 END AS is_hadir
    FROM meeting_members mm
    LEFT JOIN user_meetings um 
        ON um.meeting_id = mm.meeting_id 
       AND um.user_id = mm.user_id 
       AND um.deleted_at IS NULL
)
SELECT 
    meeting_id,
    COUNT(*) AS total_peserta,
    COALESCE(SUM(is_hadir), 0) AS hadir,
    (COUNT(*) - COALESCE(SUM(is_hadir), 0)) AS tidak_hadir,
    ROUND(
        (COALESCE(SUM(is_hadir), 0)::NUMERIC / NULLIF(COUNT(*), 0)) * 100, 
        0
    ) AS persentase_kehadiran
FROM attendance_summary
GROUP BY meeting_id;");
    }

    public function down(PDO $pdo): void {
         $pdo->exec("DROP VIEW IF EXISTS vw_meeting_stats");
    }
}
