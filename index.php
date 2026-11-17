<?php
/**
 * Punto de entrada y enrutador del sistema (patrón MVC).
 *
 * Las URL tienen la forma:   index.php?ruta=controlador/accion
 *   ?ruta=productos/listar   ->  ProductoController::listar()  (cuando exista)
 *
 * Mientras no esté listo el login, la ruta por defecto es la página de prueba
 * de conexión. Cuando exista AuthController, cambiar RUTA_POR_DEFECTO a 'auth/login'.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

const RUTA_POR_DEFECTO = 'prueba';

// ---------------------------------------------------------------------
// Autocarga de clases: busca en controllers, models y helpers
// ---------------------------------------------------------------------
spl_autoload_register(function (string $clase): void {
    foreach (['controllers', 'models', 'helpers'] as $carpeta) {
        $archivo = __DIR__ . "/app/$carpeta/$clase.php";
        if (is_file($archivo)) {
            require_once $archivo;
            return;
        }
    }
});

// ---------------------------------------------------------------------
// Ruta de prueba: confirma que PHP, el enrutador y la base de datos funcionan
// (se puede borrar cuando el sistema esté terminado)
// ---------------------------------------------------------------------
function paginaPrueba(): void
{
    $pdo = Database::getConexion();

    $usuarios  = (int) $pdo->query('SELECT COUNT(*) FROM usuario')->fetchColumn();
    $productos = (int) $pdo->query('SELECT COUNT(*) FROM producto')->fetchColumn();
    $roles     = $pdo->query('SELECT usuario, rol FROM usuario ORDER BY idUsuario')->fetchAll();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Prueba de conexión</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 640px; margin: 40px auto; padding: 0 16px; color: #1f2937; }
            .ok  { background: #dcfce7; border: 1px solid #16a34a; padding: 12px 16px; border-radius: 8px; }
            table { border-collapse: collapse; margin-top: 12px; width: 100%; }
            th, td { border: 1px solid #d1d5db; padding: 6px 10px; text-align: left; }
            th { background: #f3f4f6; }
        </style>
    </head>
    <body>
        <h1>Prueba de conexión</h1>
        <p class="ok"><strong>Conexión exitosa</strong> a la base de datos
            <code><?= htmlspecialchars(DB_NOMBRE) ?></code>.</p>
        <ul>
            <li>Versión de PHP: <?= htmlspecialchars(PHP_VERSION) ?></li>
            <li>Usuarios en la base: <?= $usuarios ?></li>
            <li>Productos en la base: <?= $productos ?></li>
        </ul>
        <table>
            <tr><th>Usuario</th><th>Rol</th></tr>
            <?php foreach ($roles as $fila): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['usuario']) ?></td>
                    <td><?= htmlspecialchars($fila['rol']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </body>
    </html>
    <?php
}

// ---------------------------------------------------------------------
// Despacho de la petición
// ---------------------------------------------------------------------
function mostrarError(int $codigo, string $mensaje): void
{
    http_response_code($codigo);
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Error</title></head><body>'
       . '<h1>' . $codigo . '</h1><p>' . htmlspecialchars($mensaje) . '</p></body></html>';
}

$ruta = $_GET['ruta'] ?? RUTA_POR_DEFECTO;

if ($ruta === 'prueba') {
    paginaPrueba();
    exit;
}

// Formato esperado: controlador/accion (solo letras, números y guion bajo)
if (!preg_match('#^([a-z_]+)(?:/([a-zA-Z_][a-zA-Z0-9_]*))?$#', $ruta, $partes)) {
    mostrarError(404, 'Página no encontrada.');
    exit;
}

$nombreControlador = ucfirst($partes[1]) . 'Controller';
$accion            = $partes[2] ?? 'index';

if (!class_exists($nombreControlador)) {
    mostrarError(404, 'Este módulo aún no está disponible.');
    exit;
}

$controlador = new $nombreControlador();

if (!method_exists($controlador, $accion) || !is_callable([$controlador, $accion])) {
    mostrarError(404, 'Página no encontrada.');
    exit;
}

$controlador->$accion();
