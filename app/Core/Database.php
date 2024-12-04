<?php

namespace LightMVC\Core;

use PDO;

class Database
{
    private static Database $instance;
    private PDO $pdo;

    private function __construct()
    {
        $config = require dirname(__DIR__, 2) . '/config/database.php';
        $this->pdo = new PDO(
            "mysql:host={$config['host']};dbname={$config['database']}",
            $config['username'],
            $config['password']
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
