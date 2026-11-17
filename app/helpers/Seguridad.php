<?php
/**
 * Utilidades de seguridad y de vistas:
 *  - escape de salida (protección contra XSS)
 *  - tokens CSRF para formularios
 *  - construcción de URL del sistema
 */
class Seguridad
{
    /** Escapa un texto para mostrarlo de forma segura en HTML. */
    public static function e(?string $texto): string
    {
        return htmlspecialchars($texto ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** URL completa hacia una ruta del enrutador, p. ej. url('auth/login'). */
    public static function url(string $ruta = ''): string
    {
        return $ruta === '' ? URL_BASE : URL_BASE . 'index.php?ruta=' . $ruta;
    }

    /** URL de un archivo de la carpeta public/, p. ej. recurso('css/estilos.css'). */
    public static function recurso(string $archivo): string
    {
        return URL_BASE . 'public/' . ltrim($archivo, '/');
    }

    /** Devuelve (y crea si hace falta) el token CSRF de la sesión. */
    public static function tokenCsrf(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    /** Campo oculto listo para pegar dentro de un <form>. */
    public static function campoCsrf(): string
    {
        return '<input type="hidden" name="csrf" value="' . self::e(self::tokenCsrf()) . '">';
    }

    /** Valida el token CSRF recibido por POST. */
    public static function validarCsrf(?string $recibido): bool
    {
        return is_string($recibido)
            && !empty($_SESSION['csrf'])
            && hash_equals($_SESSION['csrf'], $recibido);
    }
}
