<?php
$tituloPagina = 'Factura ' . $factura['numeroConsecutivo'];
$migas = ['Facturas', $factura['numeroConsecutivo']];
$rutaActiva = 'factura/listado';
require RUTA_RAIZ . '/app/views/layouts/header.php';
?>
<article class="tarjeta">
    <h1 class="titulo-1"><?= Seguridad::e($factura['numeroConsecutivo']) ?></h1>
    <p>Cliente: consumidor final</p>
    <p>Fecha: <?= Seguridad::e($factura['fechaEmision']) ?></p>
    <p>Venta: #<?= (int) $factura['idVenta'] ?></p>
    <h2 class="titulo-2">Detalle</h2>
    <?php foreach ($detalles as $detalle): ?>
        <p><?= Seguridad::e($detalle['nombre']) ?> ·
            <?= (int) $detalle['cantidad'] ?> ×
            $ <?= number_format((float) $detalle['precioUnitario'], 2, ',', '.') ?> =
            <strong>$ <?= number_format((float) $detalle['subtotal'], 2, ',', '.') ?></strong>
        </p>
    <?php endforeach; ?>
    <p class="titulo-2">Total facturado: $ <?= number_format((float) $factura['total'], 2, ',', '.') ?></p>
</article>
<?php require RUTA_RAIZ . '/app/views/layouts/footer.php'; ?>