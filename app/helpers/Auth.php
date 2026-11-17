<?php
/**
 * Control de sesión y de acceso por rol (CU-01, RF-12, RNF-07).
 *
 * Uso típico al inicio de un controlador protegido:
 *     Auth::requerirLogin();
 *     Auth::requerirRol(['Administrador']);
 */
class Auth
{
    /** Roles que pueden entrar al sistema en el MVP. */
    public const ROLES_MVP = ['Administrador', 'Cajero', 'Encargado'];

    private static bool $sesionExpirada = false;

    /** Guarda al usuario en sesión tras un login correcto. */
    public static function iniciarSesion(array $usuario): void
    {
        session_regenerate_id(true);   // evita la fijación de sesión

        $_SESSION['usuario'] = [
            'id'       => (int) $usuario['idUsuario'],
            'nombre'   => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'usuario'  => $usuario['usuario'],
            'rol'      => $usuario['rol'],
        ];
        $_SESSION['ultima_actividad'] = time();
    }

    /** ¿Hay una sesión activa y vigente? Renueva el tiempo de inactividad. */
    public static function estaAutenticado(): bool
    {
        if (empty($_SESSION['usuario'])) {
            return false;
        }

        $inactivo = time() - ($_SESSION['ultima_actividad'] ?? 0);
        if ($inactivo > SESION_MINUTOS * 60) {
            self::$sesionExpirada = true;
            self::cerrarSesion();
            return false;
        }

        $_SESSION['ultima_actividad'] = time();
        return true;
    }

    /** Datos del usuario en sesión (o null si no hay sesión). */
    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    /** Obliga a estar autenticado; si no, redirige al login. */
    public static function requerirLogin(): void
    {
        if (!self::estaAutenticado()) {
            $destino = self::$sesionExpirada ? 'auth/login&expirada=1' : 'auth/login';
            self::redirigir($destino);
        }
    }

    /** Obliga a tener uno de los roles indicados; si no, responde 403. */
    public static function requerirRol(array $roles): void
    {
        self::requerirLogin();

        if (!in_array($_SESSION['usuario']['rol'], $roles, true)) {
            http_response_code(403);
            echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Acceso denegado</title></head>'
               . '<body><h1>403</h1><p>No tienes permiso para acceder a esta sección.</p>'
               . '<p><a href="' . Seguridad::url('inicio') . '">Volver al inicio</a></p></body></html>';
            exit;
        }
    }

    /** Cierra la sesión por completo. */
    public static function cerrarSesion(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /** Redirige a una ruta del sistema y detiene la ejecución. */
    public static function redirigir(string $ruta): void
    {
        header('Location: ' . Seguridad::url($ruta));
        exit;
    }
}
