<?php
/**
 * Conexión a la base de datos mediante PDO.
 * Uso desde cualquier modelo:
 *     $pdo = Database::getConexion();
 */
class Database
{
    private static ?PDO $conexion = null;

    public static function getConexion(): PDO
    {
        if (self::$conexion === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NOMBRE . ';charset=' . DB_CHARSET;

            try {
                self::$conexion = new PDO($dsn, DB_USUARIO, DB_CLAVE, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // errores como excepciones
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // filas como arreglos asociativos
                    PDO::ATTR_EMULATE_PREPARES   => false,                    // consultas preparadas reales
                ]);
            } catch (PDOException $e) {
                // No se muestran credenciales ni detalles internos al usuario
                http_response_code(500);
                if (ENTORNO === 'desarrollo') {
                    die('Error de conexión a la base de datos: ' . htmlspecialchars($e->getMessage()));
                }
                die('No se pudo conectar con la base de datos.');
            }
        }

        return self::$conexion;
    }
}
