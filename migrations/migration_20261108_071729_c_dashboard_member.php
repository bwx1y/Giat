<?php

class migration_20261108_071729_c_dashboard_member {

    public function up(PDO $pdo): void {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE c_dashboard_member (...)";
         $pdo->exec("SELECT cron.schedule('refresh_user_dashboard_stats_hourly', '0 * * * *','REFRESH MATERIALIZED VIEW CONCURRENTLY mv_user_dashboard_member;');");
    }

    public function down(PDO $pdo): void {
         $pdo->exec("SELECT cron.unschedule('refresh_user_dashboard_stats_hourly');");
    }
}
