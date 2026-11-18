<?php
$tituloPagina = 'Alertas de stock';
$migas = ['Inventario', 'Alertas'];
$rutaActiva = 'inventario/alertas';
require RUTA_RAIZ . '/app/views/layouts/header.php';
?>
<h1 class="titulo-1">Alertas de stock</h1>
<?php if ($productos === []): ?>
    <div class="alerta alerta--exito" role="status">No hay productos activos con stock crítico.</div>
<?php else: ?>
    <div class="rejilla-modulos">
        <?php foreach ($productos as $producto): ?>
            <article class="tarjeta">
                <h2 class="titulo-3"><?= Seguridad::e($producto['nombre']) ?></h2>
                <p>Código: <?= Seguridad::e($producto['codigoBarras']) ?></p>
                <p>Existencias: <strong class="cifras"><?= (int) $producto['cantidadActual'] ?></strong></p>
                <p>Stock mínimo: <?= (int) $producto['stockMinimo'] ?></p>
                <p>Ubicación: <?= Seguridad::e($producto['ubicacion']) ?></p>
                <span class="badge badge--peligro">Stock crítico</span>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require RUTA_RAIZ . '/app/views/layouts/footer.php'; ?>