<?php

class migration_20261108_070920_mv_dashboard_member {

    public function up(PDO $pdo): void {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE mv_dashboard_member (...)";
         $pdo->exec("CREATE MATERIALIZED VIEW mv_user_dashboard_member AS
SELECT 
    u.id AS user_id,
    COALESCE(SUM(v.status), 0) AS sub_event_hadir,
    COALESCE(COUNT(v.meeting_id) - SUM(v.status), 0) AS tidak_hadir,
    ROUND((COALESCE(SUM(v.status), 0)::NUMERIC / NULLIF(COUNT(v.meeting_id), 0)) * 100, 1) AS tingkat_kehadiran,
    ((COALESCE(SUM(v.status), 0) * 3) - (COALESCE(COUNT(v.meeting_id) - SUM(v.status), 0) * 1)) AS total_point
FROM users u
LEFT JOIN vw_absent v ON u.id = v.user_id
WHERE u.deleted_at IS NULL
GROUP BY u.id;");
    }

    public function down(PDO $pdo): void {
         $pdo->exec("DROP MATERIALIZED VIEW IF EXISTS mv_user_dashboard_member");
    }
}
