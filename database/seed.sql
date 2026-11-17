-- =====================================================================
-- Sistema Web de Ventas de Supermercado - MVP (Grupo 5)
-- Datos de prueba. Ejecutar DESPUÉS de schema.sql
-- Contraseña de TODOS los usuarios de prueba: Super2026*
-- (guardada con hash bcrypt, se valida con password_verify en PHP)
-- =====================================================================

USE supermercado_db;

-- ---------------------------------------------------------------------
-- USUARIOS (uno por rol del MVP)
-- ---------------------------------------------------------------------
INSERT INTO Usuario (idUsuario, nombre, apellido, correo, usuario, `contraseña`, rol, estado) VALUES
(1, 'Laura',  'Gómez',  'admin@supermercado.test',      'admin',       '$2y$12$Kw64zzd7jQTKATQxpRTLGOQs0L9zQL1smOmsyRTcXUVsb6XhCwquW', 'Administrador', 'Activo'),
(2, 'Carlos', 'Pérez',  'cajero@supermercado.test',     'cajero1',     '$2y$12$Kw64zzd7jQTKATQxpRTLGOQs0L9zQL1smOmsyRTcXUVsb6XhCwquW', 'Cajero',        'Activo'),
(3, 'Marta',  'Ruiz',   'inventario@supermercado.test', 'encargado1',  '$2y$12$Kw64zzd7jQTKATQxpRTLGOQs0L9zQL1smOmsyRTcXUVsb6XhCwquW', 'Encargado',     'Activo');

INSERT INTO Administrador (idUsuario, nivelAcceso) VALUES (1, 'Total');
INSERT INTO Cajero (idUsuario, turno, codigoCaja) VALUES (2, 'Mañana', 'CAJA-01');
INSERT INTO EncargadoInventario (idUsuario) VALUES (3);

-- ---------------------------------------------------------------------
-- INVENTARIO (el id coincide con el id del producto del mismo número)
-- Algunos productos quedan en o por debajo del mínimo para probar
-- las alertas de stock (CU-15): ids 3, 6, 13 y 18.
-- ---------------------------------------------------------------------
INSERT INTO Inventario (idInventario, cantidadActual, stockMinimo, stockMaximo, ubicacion) VALUES
(1,   60, 20, 120, 'Pasillo 1'),
(2,   25, 10,  60, 'Refrigerador 1'),
(3,    8, 10,  50, 'Refrigerador 1'),
(4,   40, 15,  80, 'Pasillo 2'),
(5,  100, 30, 200, 'Pasillo 3'),
(6,   10, 10,  60, 'Pasillo 3'),
(7,   45, 15,  90, 'Pasillo 3'),
(8,   35, 12,  80, 'Pasillo 4'),
(9,   70, 25, 150, 'Pasillo 4'),
(10,  80, 20, 120, 'Pasillo 4'),
(11,  50, 20, 100, 'Pasillo 5'),
(12, 120, 40, 240, 'Pasillo 5'),
(13,   5, 10,  50, 'Pasillo 5'),
(14,  18,  8,  40, 'Refrigerador 2'),
(15,  22, 10,  50, 'Pasillo 2'),
(16,  55, 20, 110, 'Pasillo 6'),
(17,  30, 10,  70, 'Pasillo 7'),
(18,   3,  8,  40, 'Pasillo 7');

-- ---------------------------------------------------------------------
-- PRODUCTOS
-- ---------------------------------------------------------------------
INSERT INTO Producto (idProducto, idInventario, codigoBarras, nombre, descripcion, precioCompra, precioVenta, categoria, estado) VALUES
(1,  1,  '7701234500011', 'Leche entera 1L',          'Leche entera larga vida, bolsa de 1 litro',     3200.00,  4200.00, 'Lácteos',   'Activo'),
(2,  2,  '7701234500012', 'Queso campesino 500g',     'Queso campesino fresco, paquete de 500 g',      9500.00, 12500.00, 'Lácteos',   'Activo'),
(3,  3,  '7701234500013', 'Yogurt natural 1L',        'Yogurt natural sin azúcar, botella de 1 litro', 5200.00,  6800.00, 'Lácteos',   'Activo'),
(4,  4,  '7701234500014', 'Pan tajado 450g',          'Pan tajado blanco, paquete de 450 g',           4800.00,  6200.00, 'Panadería', 'Activo'),
(5,  5,  '7701234500015', 'Arroz blanco 1kg',         'Arroz blanco de grano largo, bolsa de 1 kg',    3500.00,  4600.00, 'Granos',    'Activo'),
(6,  6,  '7701234500016', 'Frijol cargamanto 500g',   'Frijol cargamanto seleccionado, bolsa de 500 g',5800.00,  7500.00, 'Granos',    'Activo'),
(7,  7,  '7701234500017', 'Lentejas 500g',            'Lentejas secas, bolsa de 500 g',                3200.00,  4200.00, 'Granos',    'Activo'),
(8,  8,  '7701234500018', 'Aceite vegetal 1L',        'Aceite vegetal de girasol, botella de 1 litro', 9800.00, 12900.00, 'Despensa',  'Activo'),
(9,  9,  '7701234500019', 'Azúcar blanca 1kg',        'Azúcar refinada, bolsa de 1 kg',                3600.00,  4700.00, 'Despensa',  'Activo'),
(10, 10, '7701234500020', 'Sal refinada 500g',        'Sal refinada yodada, bolsa de 500 g',           1200.00,  1800.00, 'Despensa',  'Activo'),
(11, 11, '7701234500021', 'Gaseosa cola 1.5L',        'Gaseosa sabor cola, botella de 1.5 litros',     4300.00,  5900.00, 'Bebidas',   'Activo'),
(12, 12, '7701234500022', 'Agua en botella 600ml',    'Agua potable sin gas, botella de 600 ml',       1100.00,  1800.00, 'Bebidas',   'Activo'),
(13, 13, '7701234500023', 'Jugo de naranja 1L',       'Jugo de naranja pasteurizado, caja de 1 litro', 4600.00,  6200.00, 'Bebidas',   'Activo'),
(14, 14, '7701234500024', 'Pechuga de pollo 1kg',     'Pechuga de pollo fresca, bandeja de 1 kg',     11500.00, 15500.00, 'Carnes',    'Activo'),
(15, 15, '7701234500025', 'Huevos AA x30',            'Huevos tipo AA, cubeta de 30 unidades',        13500.00, 17500.00, 'Huevos',    'Activo'),
(16, 16, '7701234500026', 'Papas fritas 150g',        'Papas fritas sabor natural, bolsa de 150 g',    3800.00,  5200.00, 'Snacks',    'Activo'),
(17, 17, '7701234500027', 'Detergente en polvo 1kg',  'Detergente en polvo multiusos, bolsa de 1 kg',  8200.00, 10900.00, 'Aseo',      'Activo'),
(18, 18, '7701234500028', 'Papel higiénico x12',      'Papel higiénico doble hoja, paquete de 12',    14500.00, 18900.00, 'Aseo',      'Activo');

-- ---------------------------------------------------------------------
-- MOVIMIENTOS INICIALES (stock de arranque cargado por el encargado)
-- ---------------------------------------------------------------------
INSERT INTO MovimientoInventario (idInventario, idUsuario, tipo, cantidad, motivo)
SELECT idInventario, 3, 'Entrada', cantidadActual, 'Stock inicial (datos de prueba)'
FROM Inventario;
