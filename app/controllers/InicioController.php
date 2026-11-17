<?php
/**
 * InicioController: pantalla de bienvenida después de iniciar sesión.
 * Muestra los módulos a los que el rol del usuario tiene acceso.
 */
class InicioController
{
    public function index(): void
    {
        Auth::requerirLogin();
        require RUTA_RAIZ . '/app/views/inicio/index.php';
    }
}
