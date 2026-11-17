-- =====================================================================
-- Sistema Web de Ventas de Supermercado - MVP (Grupo 5)
-- Esquema de base de datos (MySQL / MariaDB)
-- Origen: Modelado de Datos del DERS (MER y modelo físico)
-- Tablas incluidas: las 11 necesarias para el alcance del MVP (acta)
-- Orden de importación: 1) schema.sql  2) seed.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS supermercado_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE supermercado_db;

SET FOREIGN_KEY_CHECKS = 0;

-- Se eliminan en orden inverso para poder volver a importar el script
DROP TABLE IF EXISTS Factura;
DROP TABLE IF EXISTS Pago;
DROP TABLE IF EXISTS DetalleVenta;
DROP TABLE IF EXISTS Venta;
DROP TABLE IF EXISTS MovimientoInventario;
DROP TABLE IF EXISTS Producto;
DROP TABLE IF EXISTS Inventario;
DROP TABLE IF EXISTS EncargadoInventario;
DROP TABLE IF EXISTS Cajero;
DROP TABLE IF EXISTS Administrador;
DROP TABLE IF EXISTS Usuario;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- USUARIOS Y ROLES (CU-01, RF-12)
-- Se agregan intentosFallidos y bloqueadoHasta (no están en el MER)
-- para cumplir el bloqueo temporal por intentos fallidos del acta
-- (RNF-07).
-- ---------------------------------------------------------------------
CREATE TABLE Usuario (
  idUsuario        INT AUTO_INCREMENT PRIMARY KEY,
  nombre           VARCHAR(50)  NOT NULL,
  apellido         VARCHAR(50)  NOT NULL,
  correo           VARCHAR(100) UNIQUE,
  usuario          VARCHAR(30)  NOT NULL UNIQUE,
  `contraseña`     VARCHAR(255) NOT NULL,
  rol              ENUM('Administrador','Cajero','Cliente','Encargado','Gerente') NOT NULL,
  estado           ENUM('Activo','Inactivo') DEFAULT 'Activo',
  intentosFallidos TINYINT UNSIGNED NOT NULL DEFAULT 0,
  bloqueadoHasta   DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE Administrador (
  idUsuario   INT PRIMARY KEY,
  nivelAcceso VARCHAR(50) NOT NULL,
  CONSTRAINT fk_admin_usuario FOREIGN KEY (idUsuario)
    REFERENCES Usuario(idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Cajero (
  idUsuario  INT PRIMARY KEY,
  turno      VARCHAR(20) NOT NULL,
  codigoCaja VARCHAR(10) NOT NULL,
  CONSTRAINT fk_cajero_usuario FOREIGN KEY (idUsuario)
    REFERENCES Usuario(idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE EncargadoInventario (
  idUsuario INT PRIMARY KEY,
  CONSTRAINT fk_encargado_usuario FOREIGN KEY (idUsuario)
    REFERENCES Usuario(idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- INVENTARIO Y PRODUCTOS (CU-03, CU-04, CU-13, CU-14, CU-15)
-- Se agrega codigoBarras a Producto (no está en el MER) porque el acta
-- exige buscar productos por nombre o código de barras (RF-03).
-- ---------------------------------------------------------------------
CREATE TABLE Inventario (
  idInventario       INT AUTO_INCREMENT PRIMARY KEY,
  cantidadActual     INT NOT NULL DEFAULT 0,
  stockMinimo        INT NOT NULL,
  stockMaximo        INT NOT NULL,
  ubicacion          VARCHAR(50),
  fechaActualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                              ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT chk_inv_cantidad CHECK (cantidadActual >= 0),
  CONSTRAINT chk_inv_stock    CHECK (stockMinimo >= 0 AND stockMaximo >= stockMinimo)
) ENGINE=InnoDB;

CREATE TABLE Producto (
  idProducto   INT AUTO_INCREMENT PRIMARY KEY,
  idInventario INT NOT NULL UNIQUE,
  codigoBarras VARCHAR(20) UNIQUE,
  nombre       VARCHAR(100) NOT NULL,
  descripcion  VARCHAR(200),
  precioCompra DECIMAL(12,2) NOT NULL,
  precioVenta  DECIMAL(12,2) NOT NULL,
  categoria    VARCHAR(50) NOT NULL,
  estado       ENUM('Activo','Inactivo') DEFAULT 'Activo',
  CONSTRAINT fk_producto_inventario FOREIGN KEY (idInventario)
    REFERENCES Inventario(idInventario) ON UPDATE CASCADE,
  CONSTRAINT chk_producto_precios CHECK (precioCompra >= 0 AND precioVenta >= 0),
  INDEX idx_producto_nombre (nombre),
  INDEX idx_producto_categoria (categoria)
) ENGINE=InnoDB;

CREATE TABLE MovimientoInventario (
  idMovimiento INT AUTO_INCREMENT PRIMARY KEY,
  idInventario INT NOT NULL,
  idUsuario    INT NOT NULL,
  fecha        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tipo         ENUM('Entrada','Salida','Ajuste') NOT NULL,
  cantidad     INT NOT NULL,
  motivo       VARCHAR(100) NOT NULL,
  CONSTRAINT fk_mov_inventario FOREIGN KEY (idInventario)
    REFERENCES Inventario(idInventario) ON UPDATE CASCADE,
  CONSTRAINT fk_mov_usuario FOREIGN KEY (idUsuario)
    REFERENCES Usuario(idUsuario) ON UPDATE CASCADE,
  INDEX idx_mov_fecha (fecha)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- VENTAS, COBRO Y FACTURA (CU-05, CU-06, CU-07, CU-08)
-- idCliente e idPedido de Venta (y idPedido de Pago) quedan como columnas
-- opcionales SIN llave foránea, porque Cliente y Pedido están fuera del
-- alcance del MVP: las facturas se emiten a "consumidor final".
-- ---------------------------------------------------------------------
CREATE TABLE Venta (
  idVenta   INT AUTO_INCREMENT PRIMARY KEY,
  idUsuario INT NOT NULL,
  idCliente INT NULL,
  idPedido  INT NULL,
  fecha     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  impuesto  DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  total     DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  estado    ENUM('Abierta','Pagada','Anulada') NOT NULL DEFAULT 'Abierta',
  CONSTRAINT fk_venta_usuario FOREIGN KEY (idUsuario)
    REFERENCES Usuario(idUsuario) ON UPDATE CASCADE,
  INDEX idx_venta_fecha (fecha),
  INDEX idx_venta_estado (estado)
) ENGINE=InnoDB;

CREATE TABLE DetalleVenta (
  idDetalleVenta INT AUTO_INCREMENT PRIMARY KEY,
  idVenta        INT NOT NULL,
  idProducto     INT NOT NULL,
  cantidad       INT NOT NULL,
  precioUnitario DECIMAL(12,2) NOT NULL,
  subtotal       DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_detventa_venta FOREIGN KEY (idVenta)
    REFERENCES Venta(idVenta) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_detventa_producto FOREIGN KEY (idProducto)
    REFERENCES Producto(idProducto) ON UPDATE CASCADE,
  CONSTRAINT chk_detventa_cantidad CHECK (cantidad > 0)
) ENGINE=InnoDB;

CREATE TABLE Pago (
  idPago     INT AUTO_INCREMENT PRIMARY KEY,
  idVenta    INT NULL,
  idPedido   INT NULL,
  fechaPago  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  monto      DECIMAL(12,2) NOT NULL,
  metodoPago ENUM('Efectivo','Tarjeta','Digital','Contraentrega') NOT NULL,
  estadoPago ENUM('Pendiente','Confirmado','Rechazado') NOT NULL,
  CONSTRAINT fk_pago_venta FOREIGN KEY (idVenta)
    REFERENCES Venta(idVenta) ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Factura (
  idFactura         INT AUTO_INCREMENT PRIMARY KEY,
  idVenta           INT NOT NULL UNIQUE,
  numeroConsecutivo VARCHAR(20) NOT NULL UNIQUE,
  fechaEmision      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  total             DECIMAL(12,2) NOT NULL,
  rutaPdf           VARCHAR(200),
  CONSTRAINT fk_factura_venta FOREIGN KEY (idVenta)
    REFERENCES Venta(idVenta) ON UPDATE CASCADE
) ENGINE=InnoDB;
