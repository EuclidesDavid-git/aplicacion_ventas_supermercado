<?php
/**
 * AuthController: inicio y cierre de sesión (CU-01, RF-12).
 *
 *   ?ruta=auth/login    formulario (GET) y validación de credenciales (POST)
 *   ?ruta=auth/logout   cierre de sesión (POST)
 */
class AuthController
{
    /** Hash bcrypt de una clave aleatoria: iguala el tiempo de respuesta cuando el usuario no existe. */
    private const HASH_FALSO = '$2y$12$SxKudF9Za9cWS/44Dl6fZ.JvwlPjGd1hi9D6aUhgJrJI1chxydSXC';

    public function login(): void
    {
        if (Auth::estaAutenticado()) {
            Auth::redirigir('inicio');
        }

        $error         = null;   // credenciales inválidas o formulario incompleto
        $bloqueo       = null;   // cuenta bloqueada temporalmente
        $valorUsuario  = '';
        $expirada      = isset($_GET['expirada']);
        $cerrada       = isset($_GET['salio']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $valorUsuario = mb_substr(trim($_POST['usuario'] ?? ''), 0, 30);
            $clave        = substr((string) ($_POST['clave'] ?? ''), 0, 255);

            if (!Seguridad::validarCsrf($_POST['csrf'] ?? null)) {
                $error = 'La sesión del formulario expiró. Intenta de nuevo.';
            } elseif ($valorUsuario === '' || $clave === '') {
                $error = 'Ingresa tu usuario y tu contraseña.';
            } else {
                [$error, $bloqueo] = $this->validarCredenciales($valorUsuario, $clave);
            }
        }

        require RUTA_RAIZ . '/app/views/auth/login.php';
    }

    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && Seguridad::validarCsrf($_POST['csrf'] ?? null)) {
            Auth::cerrarSesion();
            Auth::redirigir('auth/login&salio=1');
        }

        Auth::redirigir('inicio');
    }

    /**
     * Comprueba usuario y contraseña. Si son correctos inicia sesión y redirige.
     * Devuelve [mensajeError, mensajeBloqueo] cuando el acceso no es posible.
     */
    private function validarCredenciales(string $usuario, string $clave): array
    {
        $fila  = Usuario::buscarPorUsuario($usuario);
        $ahora = time();

        // Cuenta con bloqueo temporal vigente
        if ($fila && $fila['bloqueadoHasta'] && strtotime($fila['bloqueadoHasta']) > $ahora) {
            return [null, $this->mensajeBloqueo(strtotime($fila['bloqueadoHasta']) - $ahora)];
        }

        // password_verify se ejecuta siempre (también con usuario inexistente)
        $claveValida = password_verify($clave, $fila['clave'] ?? self::HASH_FALSO);

        $accesoPermitido = $fila
            && $claveValida
            && $fila['estado'] === 'Activo'
            && in_array($fila['rol'], Auth::ROLES_MVP, true);

        if ($accesoPermitido) {
            if ((int) $fila['intentosFallidos'] > 0 || $fila['bloqueadoHasta'] !== null) {
                Usuario::reiniciarIntentos((int) $fila['idUsuario']);
            }
            Auth::iniciarSesion($fila);
            Auth::redirigir('inicio');
        }

        // Fallo: se cuenta el intento solo si el usuario existe
        $bloqueo = null;
        if ($fila) {
            $hasta = Usuario::registrarFallo((int) $fila['idUsuario'], (int) $fila['intentosFallidos']);
            if ($hasta !== null) {
                $bloqueo = $this->mensajeBloqueo(strtotime($hasta) - $ahora);
            }
        }

        return ['Credenciales inválidas. Intenta de nuevo.', $bloqueo];
    }

    private function mensajeBloqueo(int $segundosRestantes): string
    {
        $minutos = max(1, (int) ceil($segundosRestantes / 60));
        return 'Demasiados intentos fallidos. La cuenta está bloqueada temporalmente; '
             . 'intenta de nuevo en ' . $minutos . ' ' . ($minutos === 1 ? 'minuto' : 'minutos') . '.';
    }
}
