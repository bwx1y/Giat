<?php
global $applicationDb;

// 1. Tingkat Kehadiran Per Bulan (%) - dari Materialized View mv_attendance_trend_6_months (pg_cron)
$monthlyTrendStmt = queryBuilder(
    'mv_attendance_trend_6_months'
);
$monthlyTrendResult = $monthlyTrendStmt->fetchAll(PDO::FETCH_ASSOC);
//var_dump($monthlyTrendResult);

// 2. Top 5 Anggota Paling Aktif (Point) - dari Materialized View mv_user_dashboard_member (pg_cron)
$topMemberStmt = $applicationDb->query("SELECT 
    u.id AS user_id,
    u.full_name AS member_name,
    m.sub_event_hadir AS total_attended,
    m.tidak_hadir AS total_absent,
    m.total_point AS total_points
FROM mv_user_dashboard_member m
JOIN users u ON u.id = m.user_id
WHERE u.deleted_at IS NULL AND u.active = true
ORDER BY m.total_point DESC, u.full_name ASC
LIMIT 5");
$topMemberResult = $topMemberStmt->fetchAll(PDO::FETCH_ASSOC);
//var_dump($topMemberResult);

// 3. Rata-rata Kehadiran per Event Utama (%)
$eventRateStmt = $applicationDb->query("SELECT 
    event_id,
    event AS event_title,
    COUNT(DISTINCT meeting_id) AS total_meetings,
    SUM(status) AS total_attended,
    ROUND(AVG(status) * 100, 1) AS attendance_percentage
FROM vw_absent
GROUP BY event_id, event
ORDER BY attendance_percentage DESC, event_id ASC
LIMIT 3");
$eventRateResult = $eventRateStmt->fetchAll(PDO::FETCH_ASSOC);
//var_dump($eventRateResult);

// 4. Distribusi Kehadiran per Divisi
$divisionDistStmt = $applicationDb->query("SELECT 
    division,
    SUM(status) AS total_attended,
    ROUND(AVG(status) * 100, 1) AS attendance_percentage
FROM (
    SELECT 
        status,
        CASE
            WHEN event ILIKE '%UI/UX%' OR event ILIKE '%Desain%' OR event ILIKE '%Kreatif%' OR event ILIKE '%E-Sports%' OR event ILIKE '%Pameran%' THEN 'Kreatif'
            WHEN event ILIKE '%Sosial%' OR event ILIKE '%Humas%' OR event ILIKE '%Public%' OR event ILIKE '%Berita%' OR event ILIKE '%Studi Banding%' OR event ILIKE '%Makrab%' OR event ILIKE '%Donasi%' THEN 'Humas'
            WHEN event ILIKE '%Anggaran%' OR event ILIKE '%Keuangan%' OR event ILIKE '%Wirausaha%' OR event ILIKE '%Pitching%' OR event ILIKE '%Inovasi%' THEN 'Keuangan'
            WHEN event ILIKE '%Kepemimpinan%' OR event ILIKE '%LDK%' OR event ILIKE '%Ilmiah%' OR event ILIKE '%Akademik%' OR event ILIKE '%Alumni%' OR event ILIKE '%Interview%' OR event ILIKE '%FGD%' THEN 'Akademik'
            ELSE 'Teknologi'
        END AS division
    FROM vw_absent
) sub
GROUP BY division
ORDER BY total_attended DESC");
$divisionDistResult = $divisionDistStmt->fetchAll(PDO::FETCH_ASSOC);
//var_dump($divisionDistResult);
