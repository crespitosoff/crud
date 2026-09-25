<?php

/**
 * Clase Conexion
 *
 * Responsable de centralizar la conexión PDO hacia MariaDB/MySQL.
 * Usa un patrón singleton con variable estática para reutilizar la misma
 * instancia de PDO durante todo el proceso y evitar abrir múltiples
 * conexiones innecesarias por petición.
 */
class Conexion
{
    // Constantes de credenciales: se centraliza aquí la configuración
    // para no repetir el host, usuario, contraseña ni base de datos.
    const DB_HOST = 'localhost';
    const DB_NAME = 'clinica';
    const DB_USER = 'root';
    const DB_PASS = '';

    // Variable estática que guarda la instancia única de PDO.
    // Inicia en null, lo que indica que aún no se ha conectado.
    private static $pdo = null;

    /**
     * Devuelve la instancia única de la conexión PDO.
     * Si aún no existe, la crea; si ya existe, la reutiliza.
     *
     * @return PDO
     */
    public static function conectar(): PDO
    {
        // Solo se instancia PDO una vez (cuando $pdo sigue en null).
        if (self::$pdo === null) {
            try {
                // El DSN define servidor, base de datos y codificación.
                // charset=utf8mb4 permite tildes y caracteres especiales.
                self::$pdo = new PDO(
                    'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4',
                    self::DB_USER,
                    self::DB_PASS,
                    [
                        // ERRMODE_EXCEPTION: los errores lanzan PDOException
                        // en lugar de devolver false, facilitando su control.
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        // FETCH_ASSOC: cada fila se devuelve como arreglo
                        // asociativo (índices por nombre de columna).
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        // Se desactiva la emulación para usar las sentencias
                        // preparadas nativas de MariaDB (más seguras).
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ],
                );
            } catch (PDOException $e) {
                // Se relanza como RuntimeException para que el llamador la
                // capture sin exponer detalles internos de la conexión.
                throw new RuntimeException('Error de conexión a la base de datos: ' . $e->getMessage(), 0, $e);
            }
        }

        // Se retorna la instancia única (recién creada o ya existente).
        return self::$pdo;
    }
}