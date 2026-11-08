<?php

class migration_20261108_071044_idx_mv_user_dashboard_member_user_id {

    public function up(PDO $pdo): void {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE idx_mv_user_dashboard_member_user_id (...)";
         $pdo->exec("CREATE UNIQUE INDEX idx_mv_user_dashboard_member_user_id ON mv_user_dashboard_member(user_id)");
    }

    public function down(PDO $pdo): void {
         $pdo->exec("DROP INDEX IF EXISTS idx_mv_user_dashboard_member_user_id");
    }
}
