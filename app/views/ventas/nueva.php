<?php
$tituloPagina = 'Nueva venta';
$migas = ['Ventas', 'Nueva venta'];
$rutaActiva = 'venta/nueva';
require RUTA_RAIZ . '/app/views/layouts/header.php';
?>

<h1 class="titulo-1">Nueva venta</h1>
<p class="texto-suave">Agrega productos por su identificador. El catálogo y la búsqueda se integran desde sus módulos.</p>

<?php if ($error !== null): ?>
    <div class="alerta alerta--error" role="alert">
        <span><?= Seguridad::e($error) ?></span>
    </div>
<?php endif; ?>
<?php if (!empty($exito)): ?>
    <div class="alerta alerta--exito" role="status">
        <span><?= Seguridad::e($exito) ?></span>
    </div>
<?php endif; ?>

<section class="tarjeta" aria-labelledby="titulo-agregar">
    <h2 class="titulo-2" id="titulo-agregar">Agregar producto</h2>
    <div class="campo">
        <label class="campo__etiqueta" for="buscarProducto">Buscar por nombre o código de barras</label>
        <input class="campo__control" type="search" id="buscarProducto" autocomplete="off">
    </div>
    <div id="resultadosBusqueda" class="rejilla-modulos" aria-live="polite"></div>
    <form class="formulario" method="post" action="<?= Seguridad::url('venta/agregar') ?>">
        <?= Seguridad::campoCsrf() ?>
        <input type="hidden" name="accion" value="agregar">
        <div class="campo">
            <label class="campo__etiqueta" for="idProducto">Identificador del producto</label>
            <input class="campo__control" type="number" id="idProducto" name="idProducto"
                   min="1" step="1" required>
        </div>
        <div class="campo">
            <label class="campo__etiqueta" for="cantidad">Cantidad</label>
            <input class="campo__control" type="number" id="cantidad" name="cantidad"
                   min="1" step="1" required>
        </div>
        <button class="btn btn--primario" type="submit">Agregar</button>
    </form>
</section>

<section aria-labelledby="titulo-carrito">
    <h2 class="titulo-2" id="titulo-carrito">Productos de la venta</h2>
    <?php if ($lineas === []): ?>
        <p class="texto-suave">Aún no has agregado productos.</p>
    <?php else: ?>
        <div class="rejilla-modulos">
            <?php foreach ($lineas as $linea): ?>
                <article class="tarjeta">
                    <h3 class="titulo-3"><?= Seguridad::e($linea['nombre']) ?></h3>
                    <p class="texto-pequeno texto-suave">
                        ID <?= (int) $linea['idProducto'] ?>
                        <?php if ($linea['codigoBarras'] !== null): ?>
                            · Código <?= Seguridad::e($linea['codigoBarras']) ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!$linea['disponible']): ?>
                        <div class="alerta alerta--error" role="alert">Producto no disponible.</div>
                    <?php else: ?>
                        <p>Precio unitario: <span class="cifras">$ <?= number_format($linea['precioUnitario'], 2, ',', '.') ?></span></p>
                        <p>Existencias consultadas: <?= (int) $linea['stock'] ?></p>
                        <p>Subtotal: <strong class="cifras">$ <?= number_format($linea['subtotal'], 2, ',', '.') ?></strong></p>
                        <form class="formulario" method="post" action="<?= Seguridad::url('venta/nueva') ?>">
                            <?= Seguridad::campoCsrf() ?>
                            <input type="hidden" name="accion" value="actualizar">
                            <input type="hidden" name="idProducto" value="<?= (int) $linea['idProducto'] ?>">
                            <div class="campo">
                                <label class="campo__etiqueta" for="cantidad-<?= (int) $linea['idProducto'] ?>">Cantidad</label>
                                <input class="campo__control" type="number"
                                       id="cantidad-<?= (int) $linea['idProducto'] ?>" name="cantidad"
                                       value="<?= (int) $linea['cantidad'] ?>" min="1"
                                       max="<?= (int) $linea['stock'] ?>" step="1" required>
                            </div>
                            <button class="btn btn--outline btn--pequeno" type="submit">Actualizar cantidad</button>
                        </form>
                    <?php endif; ?>
                    <form class="formulario" method="post" action="<?= Seguridad::url('venta/procesar') ?>">
                        <?= Seguridad::campoCsrf() ?>
                        <input type="hidden" name="accion" value="quitar">
                        <input type="hidden" name="idProducto" value="<?= (int) $linea['idProducto'] ?>">
                        <button class="btn btn--peligro btn--pequeno" type="submit">Quitar</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>

        <section class="tarjeta" aria-label="Total de la venta">
            <p class="titulo-3">Total (precios con IVA incluido)</p>
            <p class="titulo-2 cifras">$ <?= number_format($total, 2, ',', '.') ?></p>
            <form class="formulario" method="post" action="<?= Seguridad::url('venta/nueva') ?>">
                <?= Seguridad::campoCsrf() ?>
                <input type="hidden" name="accion" value="confirmar">
                <button class="btn btn--acento btn--grande" type="submit">Confirmar venta y continuar al cobro</button>
            </form>
        </section>
    <?php endif; ?>
</section>

    <script src="<?= Seguridad::recurso('js/busqueda.js') ?>"></script>
<?php require RUTA_RAIZ . '/app/views/layouts/footer.php'; ?>