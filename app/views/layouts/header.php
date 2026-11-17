<?php
/**
 * Encabezado común de las pantallas internas: <head>, menú lateral y barra superior.
 * Variables que debe definir la vista antes de incluirlo:
 *   $tituloPagina  (string)  título de la pestaña
 *   $migas         (array)   ruta de navegación, p. ej. ['Ventas', 'Nueva venta']
 *   $rutaActiva    (string)  ruta del módulo actual, p. ej. 'venta/nueva'
 */
$usuarioActual = Auth::usuario();

// Módulos del menú y roles que pueden verlos (cada rol ve solo lo suyo)
$modulos = [
    ['ruta' => 'venta/nueva',            'etiqueta' => 'Ventas',           'icono' => 'shopping-cart',
     'detalle' => 'Registra y cobra ventas en el punto de pago.',          'roles' => ['Administrador', 'Cajero']],
    ['ruta' => 'factura/listado',        'etiqueta' => 'Facturas',         'icono' => 'receipt',
     'detalle' => 'Consulta las facturas emitidas.',                       'roles' => ['Administrador', 'Cajero']],
    ['ruta' => 'producto/listar',        'etiqueta' => 'Productos',        'icono' => 'clipboard-list',
     'detalle' => 'Administra el catálogo de productos.',                  'roles' => ['Administrador']],
    ['ruta' => 'inventario/existencias', 'etiqueta' => 'Inventario',       'icono' => 'package',
     'detalle' => 'Consulta las existencias de cada producto.',            'roles' => ['Administrador', 'Encargado']],
    ['ruta' => 'inventario/alertas',     'etiqueta' => 'Alertas de stock', 'icono' => 'bell',
     'detalle' => 'Revisa los productos en o bajo el stock mínimo.',       'roles' => ['Administrador', 'Encargado']],
];

$menu = array_filter($modulos, fn(array $m): bool => in_array($usuarioActual['rol'], $m['roles'], true));

$etiquetasRol = ['Administrador' => 'Administrador', 'Cajero' => 'Cajero', 'Encargado' => 'Encargado de inventario'];
$rolVisible   = $etiquetasRol[$usuarioActual['rol']] ?? $usuarioActual['rol'];
$iniciales    = mb_strtoupper(mb_substr($usuarioActual['nombre'], 0, 1) . mb_substr($usuarioActual['apellido'], 0, 1));
$migas        = $migas ?? [$tituloPagina ?? 'Inicio'];
$rutaActiva   = $rutaActiva ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Seguridad::e(($tituloPagina ?? 'Sistema') . ' · Supermercado POS') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&amp;family=Inter:wght@400;500;600&amp;family=Poppins:wght@500;600;700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= Seguridad::recurso('css/estilos.css') ?>">
</head>
<body>
<div class="app">

    <?php require __DIR__ . '/sidebar.php'; ?>

    <div class="app__principal">
        <header class="barra-superior">
            <nav class="migas" aria-label="Ruta de navegación">
                <?php foreach ($migas as $i => $miga): ?>
                    <?php if ($i > 0): ?><span class="migas__separador">›</span><?php endif; ?>
                    <span class="<?= $i === count($migas) - 1 ? 'migas__actual' : '' ?>"><?= Seguridad::e($miga) ?></span>
                <?php endforeach; ?>
            </nav>

            <div class="barra-superior__usuario">
                <div class="barra-superior__datos">
                    <strong><?= Seguridad::e($usuarioActual['nombre'] . ' ' . $usuarioActual['apellido']) ?></strong>
                    <span><?= Seguridad::e($rolVisible) ?></span>
                </div>
                <div class="avatar" aria-hidden="true"><?= Seguridad::e($iniciales) ?></div>
            </div>
        </header>

        <main class="contenido">
