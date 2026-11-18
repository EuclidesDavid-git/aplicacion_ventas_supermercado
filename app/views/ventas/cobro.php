<?php
$tituloPagina = 'Cobro de venta';
$migas = ['Ventas', 'Cobro'];
$rutaActiva = 'venta/nueva';
require RUTA_RAIZ . '/app/views/layouts/header.php';
?>

<h1 class="titulo-1">Cobro</h1>

<?php if ($error !== null): ?>
    <div class="alerta alerta--error" role="alert">
        <span><?= Seguridad::e($error) ?></span>
    </div>
<?php endif; ?>

<?php if ($resultado !== null): ?>
    <section class="tarjeta" aria-labelledby="titulo-pago-confirmado">
        <div class="alerta alerta--exito" role="status">
            <strong id="titulo-pago-confirmado">Pago registrado correctamente.</strong>
        </div>
        <p>Método: <?= Seguridad::e($resultado['metodoPago']) ?></p>
        <p>Total: <strong class="cifras">$ <?= number_format($resultado['total'], 2, ',', '.') ?></strong></p>
        <?php if ($resultado['metodoPago'] === 'Efectivo'): ?>
            <p>Valor recibido: <span class="cifras">$ <?= number_format($resultado['recibido'], 2, ',', '.') ?></span></p>
            <p>Cambio: <strong class="cifras">$ <?= number_format($resultado['cambio'], 2, ',', '.') ?></strong></p>
        <?php endif; ?>
        <p>Estado de la venta: <?= Seguridad::e($venta['estado'] ?? 'Pagada') ?></p>
        <?php if (!empty($resultado['idFactura'])): ?>
            <p>
                <a class="btn btn--outline" href="<?= Seguridad::url('factura/ver&id=' . (int) $resultado['idFactura']) ?>">Ver factura</a>
            </p>
        <?php endif; ?>
        <a class="btn btn--primario" href="<?= Seguridad::url('venta/nueva') ?>">Iniciar otra venta</a>
    </section>
<?php elseif ($venta !== null): ?>
    <section class="tarjeta" aria-labelledby="titulo-resumen">
        <h2 class="titulo-2" id="titulo-resumen">Venta #<?= (int) $venta['idVenta'] ?></h2>
        <p>Fecha: <?= Seguridad::e($venta['fecha']) ?></p>
        <p>Estado: <?= Seguridad::e($venta['estado']) ?></p>
        <h3 class="titulo-3">Detalle</h3>
        <?php foreach ($detalles as $detalle): ?>
            <p>
                Producto #<?= (int) $detalle['idProducto'] ?> ·
                <?= (int) $detalle['cantidad'] ?> ×
                $ <?= number_format((float) $detalle['precioUnitario'], 2, ',', '.') ?> =
                <strong class="cifras">$ <?= number_format((float) $detalle['subtotal'], 2, ',', '.') ?></strong>
            </p>
        <?php endforeach; ?>
        <p class="titulo-2">Total: <span class="cifras">$ <?= number_format((float) $venta['total'], 2, ',', '.') ?></span></p>

        <?php if ($venta['estado'] === 'Abierta' && $error === null): ?>
            <form class="formulario" method="post"
                  action="<?= Seguridad::url('pago/cobrar&idVenta=' . (int) $venta['idVenta']) ?>">
                <?= Seguridad::campoCsrf() ?>
                <input type="hidden" name="idVenta" value="<?= (int) $venta['idVenta'] ?>">
                <div class="campo">
                    <label class="campo__etiqueta" for="metodo">Método de pago</label>
                    <select class="campo__control" id="metodo" name="metodo" required>
                        <option value="">Selecciona un método</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Pago digital">Pago digital</option>
                    </select>
                </div>
                <div class="campo">
                    <label class="campo__etiqueta" for="recibido">Valor recibido (solo efectivo)</label>
                    <input class="campo__control" type="number" id="recibido" name="recibido"
                           min="0" step="0.01" inputmode="decimal">
                    <span class="campo__ayuda">Para tarjeta y pago digital este campo se ignora.</span>
                </div>
                <button class="btn btn--acento btn--grande" type="submit">Registrar cobro</button>
            </form>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php require RUTA_RAIZ . '/app/views/layouts/footer.php'; ?>