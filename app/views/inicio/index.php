<?php
/** Pantalla de inicio: saludo y accesos a los módulos del rol. */
$tituloPagina = 'Inicio';
$migas        = ['Inicio'];
$rutaActiva   = 'inicio';

require RUTA_RAIZ . '/app/views/layouts/header.php';
?>

<h1 class="titulo-1">Hola, <?= Seguridad::e($usuarioActual['nombre']) ?></h1>
<p class="texto-suave">Estás conectado como <strong><?= Seguridad::e($rolVisible) ?></strong>. Elige un módulo para empezar.</p>

<section class="rejilla-modulos" aria-label="Módulos disponibles">
    <?php foreach ($menu as $item): ?>
        <a class="tarjeta tarjeta--enlace" href="<?= Seguridad::url($item['ruta']) ?>">
            <span class="tarjeta__icono"><?= Iconos::svg($item['icono'], 24) ?></span>
            <h2 class="titulo-3"><?= Seguridad::e($item['etiqueta']) ?></h2>
            <p class="texto-suave"><?= Seguridad::e($item['detalle']) ?></p>
        </a>
    <?php endforeach; ?>
</section>

<?php require RUTA_RAIZ . '/app/views/layouts/footer.php'; ?>
