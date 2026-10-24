<?php

class migration_20261023_035510_vw_member_event
{

    public function up(PDO $pdo): void
    {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE vw_member_event (...)";
        $pdo->exec('CREATE OR REPLACE VIEW vw_member_event AS
SELECT user_events.id,
       user_events.role,
       users.full_name,
       users.username
FROM user_events
         JOIN users ON users.id = user_events.user_id
WHERE users.deleted_at IS NULL
  AND user_events.deleted_at IS NULL
ORDER BY user_events.id DESC');
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP VIEW IF EXISTS vw_member_event");
    }
}
