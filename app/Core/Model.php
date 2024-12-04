<?php

namespace LightMVC\Core;

use PDO;

abstract class Model
{
    protected static string $table;
    protected ?PDO $db = null;

    public function __construct()
    {
//        $this->db = new PDO(
//            "mysql:host=localhost;dbname=your_database",
//            "username",
//            "password"
//        );
    }

    public static function all(): array
    {
        $instance = new static();
        $stmt = $instance->db->query("SELECT * FROM " . static::$table);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id)
    {
        $instance = new static();
        $stmt = $instance->db?->prepare("SELECT * FROM " . static::$table . " WHERE id = ?");

        if (!$stmt) {
            return null;
        }

        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
