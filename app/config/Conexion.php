<?php

class Conexion
{
    const DB_HOST = 'localhost';
    const DB_NAME = 'clinica';
    const DB_USER = 'root';
    const DB_PASS = '';

    private static $pdo = null;

    public static function conectar(): PDO
    {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4',
                    self::DB_USER,
                    self::DB_PASS,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ],
                );
            } catch (PDOException $e) {
                throw new RuntimeException('Error de conexión a la base de datos: ' . $e->getMessage(), 0, $e);
            }
        }

        return self::$pdo;
    }
}
