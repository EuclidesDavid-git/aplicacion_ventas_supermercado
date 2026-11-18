<?php
$tituloPagina = 'Existencias';
$migas = ['Inventario', 'Existencias'];
$rutaActiva = 'inventario/existencias';
require RUTA_RAIZ . '/app/views/layouts/header.php';
?>
<h1 class="titulo-1">Existencias</h1>
<section class="tarjeta">
    <form class="formulario" method="get" action="<?= Seguridad::url('inventario/existencias') ?>">
        <input type="hidden" name="ruta" value="inventario/existencias">
        <div class="campo">
            <label class="campo__etiqueta" for="q">Buscar por nombre o código</label>
            <input class="campo__control" type="text" id="q" name="q"
                   value="<?= Seguridad::e($busqueda) ?>" maxlength="100">
        </div>
        <button class="btn btn--primario" type="submit">Buscar</button>
    </form>
</section>
<?php if ($productos === []): ?>
    <p class="texto-suave">No se encontraron productos.</p>
<?php else: ?>
    <div class="rejilla-modulos">
        <?php foreach ($productos as $producto): ?>
            <article class="tarjeta">
                <h2 class="titulo-3"><?= Seguridad::e($producto['nombre']) ?></h2>
                <p>Código: <?= Seguridad::e($producto['codigoBarras']) ?></p>
                <p>Existencias: <strong><?= (int) $producto['cantidadActual'] ?></strong></p>
                <p>Mínimo: <?= (int) $producto['stockMinimo'] ?> · Máximo: <?= (int) $producto['stockMaximo'] ?></p>
                <p>Ubicación: <?= Seguridad::e($producto['ubicacion']) ?></p>
                <span class="badge <?= $producto['estado'] === 'Activo' ? 'badge--exito' : 'badge--neutro' ?>">
                    <?= Seguridad::e($producto['estado']) ?>
                </span>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require RUTA_RAIZ . '/app/views/layouts/footer.php'; ?>