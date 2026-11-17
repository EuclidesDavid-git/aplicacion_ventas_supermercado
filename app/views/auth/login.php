<?php
/**
 * Pantalla de inicio de sesión.
 * Estructura: mockup "Desktop - 1" del Figma (Sprint 3).
 * Medidas, colores y tipografía: guía de estilos del DERS.
 *
 * Variables recibidas desde AuthController::login():
 *   $error, $bloqueo, $valorUsuario, $expirada, $cerrada
 */
$hayError = $error !== null;
$esDesarrollo = ENTORNO === 'desarrollo';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · Supermercado POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&amp;family=Inter:wght@400;500;600&amp;family=Poppins:wght@500;600;700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= Seguridad::recurso('css/estilos.css') ?>">
</head>
<body class="pagina-login">
<div class="login">

    <!-- Panel verde de la marca -->
    <aside class="login__marca">
        <div class="login__marca-superior">
            <span class="logo logo--dorado logo--circulo"><?= Iconos::svg('store', 20) ?></span>
            <span class="login__marca-superior-nombre">Supermercado POS</span>
        </div>

        <div class="login__marca-central">
            <span class="login__marca-icono"><?= Iconos::svg('store', 32) ?></span>
            <h2 class="login__marca-titulo">Sistema Web de Ventas de Supermercado</h2>
            <p class="login__marca-texto">Caja, inventario y facturación en un solo lugar, con acceso según el rol de cada persona.</p>
        </div>

        <p class="login__marca-pie">© 2026 Supermercado POS · v1.0</p>
    </aside>

    <!-- Formulario -->
    <main class="login__zona-formulario">
        <div class="login__contenido">

            <section class="tarjeta login__tarjeta" aria-labelledby="titulo-login">

                <div class="login__marca-movil">
                    <span class="logo logo--dorado logo--circulo"><?= Iconos::svg('store', 20) ?></span>
                    <span class="login__marca-movil-nombre">Supermercado POS</span>
                </div>

                <h1 class="titulo-2" id="titulo-login">Iniciar sesión</h1>
                <p class="texto-pequeno texto-suave login__subtitulo">Ingresa tus credenciales para continuar.</p>

                <?php if ($bloqueo !== null): ?>
                    <div class="alerta alerta--aviso" role="alert">
                        <?= Iconos::svg('triangle-alert', 20) ?>
                        <div><strong>Cuenta bloqueada:</strong> <?= Seguridad::e($bloqueo) ?></div>
                    </div>
                <?php endif; ?>

                <?php if ($expirada): ?>
                    <div class="alerta alerta--aviso" role="alert">
                        <?= Iconos::svg('triangle-alert', 20) ?>
                        <div><strong>Sesión expirada:</strong> por inactividad se cerró tu sesión. Ingresa de nuevo.</div>
                    </div>
                <?php endif; ?>

                <?php if ($cerrada): ?>
                    <div class="alerta alerta--exito" role="status">
                        <?= Iconos::svg('circle-check', 20) ?>
                        <div><strong>Sesión cerrada:</strong> saliste del sistema correctamente.</div>
                    </div>
                <?php endif; ?>

                <form class="formulario" method="post" action="<?= Seguridad::url('auth/login') ?>" novalidate>
                    <?= Seguridad::campoCsrf() ?>

                    <div class="campo">
                        <label class="campo__etiqueta" for="usuario">Usuario</label>
                        <input class="campo__control <?= $hayError ? 'campo__control--error' : '' ?>"
                               type="text" id="usuario" name="usuario"
                               value="<?= Seguridad::e($valorUsuario) ?>"
                               placeholder="p. ej. cajero"
                               maxlength="30" autocomplete="username" required
                               <?= $valorUsuario === '' ? 'autofocus' : '' ?>
                               <?= $hayError ? 'aria-invalid="true" aria-describedby="error-login"' : '' ?>>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="clave">Contraseña</label>
                        <div class="campo__envoltorio">
                            <input class="campo__control <?= $hayError ? 'campo__control--error' : '' ?>"
                                   type="password" id="clave" name="clave"
                                   maxlength="255" autocomplete="current-password" required
                                   <?= $valorUsuario !== '' ? 'autofocus' : '' ?>
                                   <?= $hayError ? 'aria-invalid="true" aria-describedby="error-login"' : '' ?>>
                            <button type="button" class="campo__ojo" id="alternar-clave"
                                    aria-label="Mostrar contraseña" aria-pressed="false">
                                <span class="ojo--ver"><?= Iconos::svg('eye', 18) ?></span>
                                <span class="ojo--ocultar" hidden><?= Iconos::svg('eye-off', 18) ?></span>
                            </button>
                        </div>

                        <?php if ($hayError): ?>
                            <p class="campo__mensaje-error" id="error-login" role="alert">
                                <?= Iconos::svg('circle-alert', 16) ?>
                                <span><?= Seguridad::e($error) ?></span>
                            </p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn--primario btn--grande btn--completo">Ingresar</button>
                </form>
            </section>

            <?php if ($esDesarrollo): ?>
                <!-- Solo se muestra con ENTORNO = 'desarrollo' (config/config.php) -->
                <section class="accesos-prueba" aria-label="Acceso rápido de prueba">
                    <p class="accesos-prueba__titulo">Acceso rápido de prueba</p>
                    <div class="chips">
                        <button type="button" class="chip" data-usuario="admin"      data-clave="Super2026*">Administrador</button>
                        <button type="button" class="chip" data-usuario="cajero1"    data-clave="Super2026*">Cajero</button>
                        <button type="button" class="chip" data-usuario="encargado1" data-clave="Super2026*">Encargado</button>
                    </div>
                </section>
            <?php endif; ?>

        </div>
    </main>

</div>

<script>
(function () {
    var campoClave = document.getElementById('clave');
    var campoUsuario = document.getElementById('usuario');
    var ojo = document.getElementById('alternar-clave');

    // Mostrar u ocultar la contraseña
    ojo.addEventListener('click', function () {
        var visible = campoClave.type === 'text';
        campoClave.type = visible ? 'password' : 'text';
        ojo.setAttribute('aria-pressed', visible ? 'false' : 'true');
        ojo.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
        ojo.querySelector('.ojo--ver').hidden = !visible;
        ojo.querySelector('.ojo--ocultar').hidden = visible;
    });

    // Accesos rápidos de prueba: rellenan usuario y contraseña
    document.querySelectorAll('.chip[data-usuario]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            campoUsuario.value = chip.getAttribute('data-usuario');
            campoClave.value = chip.getAttribute('data-clave');
            campoClave.focus();
        });
    });
})();
</script>
</body>
</html>
