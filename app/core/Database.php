<?php
/**
 * Conexión única a MySQL (patrón singleton).
 * Cualquier modelo pide la conexión con Database::conexion().
 */

declare(strict_types=1);

final class Database
{
    private static ?PDO $conexion = null;

    public static function conexion(): PDO
    {
        if (self::$conexion === null) {
            try {
                self::$conexion = new PDO(
                    'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NOMBRE . ';charset=utf8mb4',
                    DB_USUARIO,
                    DB_CLAVE,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $error) {
                error_log('Error de conexión a MySQL: ' . $error->getMessage());
                http_response_code(500);
                exit('No se pudo conectar con la base de datos. Revisa app/config/config.php.');
            }
        }

        return self::$conexion;
    }
}
