<?php

class migration_20261025_080450_idx_attendance_trend_month {

    public function up(PDO $pdo): void {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE idx_attendance_trend_month (...)";
         $pdo->exec("CREATE UNIQUE INDEX uq_attendance_trend_month ON mv_attendance_trend_6_months(month)");
    }

    public function down(PDO $pdo): void {
         $pdo->exec("DROP TABLE IF EXISTS idx_attendance_trend_month");
    }
}
