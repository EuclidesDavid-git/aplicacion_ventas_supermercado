<?php
/**
 * Menú lateral (240 px, verde oscuro). Cada rol ve solo sus módulos.
 * Usa $menu y $rutaActiva definidos en header.php.
 */
?>
<aside class="menu-lateral">
    <a class="menu-lateral__marca" href="<?= Seguridad::url('inicio') ?>">
        <span class="logo logo--dorado"><?= Iconos::svg('store', 20) ?></span>
        <span class="menu-lateral__nombre">Supermercado POS</span>
    </a>

    <nav class="menu-lateral__nav" aria-label="Módulos">
        <?php foreach ($menu as $item): ?>
            <a class="menu-lateral__enlace <?= $rutaActiva === $item['ruta'] ? 'menu-lateral__enlace--activo' : '' ?>"
               href="<?= Seguridad::url($item['ruta']) ?>"
               <?= $rutaActiva === $item['ruta'] ? 'aria-current="page"' : '' ?>>
                <?= Iconos::svg($item['icono'], 20) ?>
                <span><?= Seguridad::e($item['etiqueta']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <form class="menu-lateral__salir" method="post" action="<?= Seguridad::url('auth/logout') ?>">
        <?= Seguridad::campoCsrf() ?>
        <button type="submit" class="menu-lateral__enlace menu-lateral__boton">
            <?= Iconos::svg('log-out', 20) ?>
            <span>Cerrar sesión</span>
        </button>
    </form>
</aside>
