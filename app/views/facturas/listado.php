<?php
$tituloPagina = 'Facturas';
$migas = ['Facturas'];
$rutaActiva = 'factura/listado';
require RUTA_RAIZ . '/app/views/layouts/header.php';
?>
<h1 class="titulo-1">Facturas</h1>
<?php if ($facturas === []): ?>
    <p class="texto-suave">Aún no hay facturas emitidas.</p>
<?php else: ?>
    <div class="tabla-contenedor">
        <table class="tabla">
            <thead><tr><th>Factura</th><th>Venta</th><th>Fecha</th><th>Total</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($facturas as $factura): ?>
                <tr>
                    <td><?= Seguridad::e($factura['numeroConsecutivo']) ?></td>
                    <td>#<?= (int) $factura['idVenta'] ?></td>
                    <td><?= Seguridad::e($factura['fechaEmision']) ?></td>
                    <td class="cifras">$ <?= number_format((float) $factura['total'], 2, ',', '.') ?></td>
                    <td><a class="btn btn--outline btn--pequeno"
                           href="<?= Seguridad::url('factura/ver&id=' . (int) $factura['idFactura']) ?>">Ver</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require RUTA_RAIZ . '/app/views/layouts/footer.php'; ?>