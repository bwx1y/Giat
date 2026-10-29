<?php

class migration_20261022_000000_initialize
{

    public function up(PDO $pdo): void
    {
        // Write logic to create table / add columns here
        // $sql = "CREATE TABLE initialize (...)";
        $pdo->exec('CREATE EXTENSION IF NOT EXISTS pg_trgm;');
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec('DROP EXTENSION IF EXISTS pg_trgm');
    }
}
