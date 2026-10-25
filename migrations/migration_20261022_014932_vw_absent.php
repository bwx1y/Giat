<?php

class migration_20261022_014932_vw_absent
{

    public function up(PDO $pdo): void
    {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE vw_absent (...)";
        $pdo->exec("CREATE OR REPLACE VIEW vw_absent AS
SELECT user_meetings.id,
       events.id                       AS event_id,
       users.full_name                 AS member_name,
       events.title                    AS event,
       meetings.title                  AS sub_event,
       meetings.start_date             AS date,
       user_meetings.attendance_method AS method,
       CASE
           WHEN user_meetings.id IS NOT NULL THEN 1
           ELSE 0
           END                         AS status
FROM users
         JOIN user_events ON user_events.user_id = users.id AND user_events.deleted_at IS NULL
         JOIN events ON events.id = user_events.event_id AND events.deleted_at IS NULL
         JOIN meetings ON meetings.event_id = events.id AND meetings.deleted_at IS NULL
         LEFT JOIN user_meetings ON user_meetings.user_id = users.id
    AND user_meetings.meeting_id = meetings.id
WHERE users.deleted_at IS NULL
  AND events.deleted_at IS NULL
  AND meetings.deleted_at IS NULL
ORDER BY meetings.created_at");
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP VIEW IF EXISTS vw_absent");
    }
}
