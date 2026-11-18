<?php
$tituloPagina = 'Productos';
$migas = ['Productos'];
$rutaActiva = 'producto/listar';
require RUTA_RAIZ . '/app/views/layouts/header.php';
?>

<h1 class="titulo-1">Catálogo de productos</h1>

<?php if (!empty($exito)): ?>
    <div class="alerta alerta--exito" role="status">
        <span><?= Seguridad::e($exito) ?></span>
    </div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alerta alerta--error" role="alert">
        <span><?= Seguridad::e($error) ?></span>
    </div>
<?php endif; ?>

<p>
    <a class="btn btn--primario" href="<?= Seguridad::url('producto/crear') ?>">Nuevo producto</a>
</p>

<?php if ($productos === []): ?>
    <p class="texto-suave">No hay productos registrados.</p>
<?php else: ?>
    <div class="rejilla-modulos">
        <?php foreach ($productos as $producto): ?>
            <article class="tarjeta">
                <h2 class="titulo-3"><?= Seguridad::e($producto['nombre']) ?></h2>
                <p class="texto-pequeno texto-suave">
                    Código: <?= Seguridad::e($producto['codigoBarras']) ?>
                </p>
                <p>Categoría: <?= Seguridad::e($producto['categoria']) ?></p>
                <p>Precio de venta: <strong class="cifras">$ <?= number_format((float) $producto['precioVenta'], 2, ',', '.') ?></strong></p>
                <p>Existencias: <strong class="cifras"><?= (int) $producto['cantidadActual'] ?></strong></p>
                <p>
                    <span class="badge <?= $producto['estado'] === 'Activo' ? 'badge--exito' : 'badge--neutro' ?>">
                        <?= Seguridad::e($producto['estado']) ?>
                    </span>
                </p>

                <form class="formulario" method="get" action="<?= Seguridad::url('producto/editar') ?>">
                    <input type="hidden" name="ruta" value="producto/editar">
                    <input type="hidden" name="id" value="<?= (int) $producto['idProducto'] ?>">
                    <button class="btn btn--outline btn--pequeno" type="submit">Editar</button>
                </form>
                <?php if ($producto['estado'] === 'Activo'): ?>
                    <form class="formulario" method="post" action="<?= Seguridad::url('producto/baja') ?>">
                        <?= Seguridad::campoCsrf() ?>
                        <input type="hidden" name="idProducto" value="<?= (int) $producto['idProducto'] ?>">
                        <button class="btn btn--peligro btn--pequeno" type="submit"
                                onclick="return confirm('¿Dar de baja este producto?');">Dar de baja</button>
                    </form>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require RUTA_RAIZ . '/app/views/layouts/footer.php'; ?>