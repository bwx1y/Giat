<?php

class migration_20261025_132509_c_attendance_trend_month
{

    public function up(PDO $pdo): void
    {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE c_attendance_trend_month (...)";
        $pdo->exec("SELECT cron.schedule('refresh_attendance_trend_hourly', '0 * * * *','REFRESH MATERIALIZED VIEW CONCURRENTLY mv_attendance_trend_6_months;')");
    }

    public function down(PDO $pdo): void
    {
         $pdo->exec("SELECT cron.unschedule('refresh_attendance_trend_hourly')");
    }
}
