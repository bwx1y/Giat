<?php

class migration_20261025_011246_mv_attendance_trend_6_months
{

    public function up(PDO $pdo): void
    {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE vw_attendance_trend_6_months (...)";
        $pdo->exec("CREATE MATERIALIZED VIEW mv_attendance_trend_6_months AS
WITH active_users AS (
    SELECT COUNT(*) AS total_users
    FROM users
    WHERE active = true AND deleted_at IS NULL
),
monthly_summary AS (
    SELECT
        DATE_TRUNC('month', m.start_date) AS month_date,
        TO_CHAR(m.start_date, 'Mon') AS month,
        COUNT(DISTINCT m.id) AS total_meetings,
        COUNT(um.id) AS total_attendance
    FROM meetings m
    LEFT JOIN user_meetings um ON m.id = um.meeting_id AND um.deleted_at IS NULL
    WHERE m.start_date >= NOW() - INTERVAL '6 month'
      AND m.deleted_at IS NULL
    GROUP BY DATE_TRUNC('month', m.start_date), TO_CHAR(m.start_date, 'Mon')
)
SELECT
    ms.month,
    ms.total_meetings,
    ms.total_attendance,
    ROUND(
        (ms.total_attendance * 100.0) / NULLIF(ms.total_meetings * au.total_users, 0),
        1
    ) AS attendance_percentage
FROM monthly_summary ms
CROSS JOIN active_users au
ORDER BY ms.month_date ASC");
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP MATERIALIZED VIEW IF EXISTS mv_attendance_trend_6_months");
    }
}
