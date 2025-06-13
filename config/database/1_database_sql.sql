- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema pawpro_database
-- -----------------------------------------------------
CREATE DATABASE IF NOT EXISTS `pawpro_database`;
USE `pawpro_database` ;

-- -----------------------------------------------------
-- Table `pawpro_database`.`Clientes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`Clientes` (
  `cliente_id` INT NOT NULL AUTO_INCREMENT,
  `dni` VARCHAR(8) NOT NULL COMMENT 'DNI del cliente',
  `nombre_cliente` VARCHAR(200) NOT NULL,
  `direccion` VARCHAR(255) NULL DEFAULT NULL,
  `telefono` VARCHAR(20) NULL DEFAULT NULL,
  `correo_electronico` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Campo adicional útil para comunicaciones',
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cliente_id`),
  UNIQUE INDEX `dni` (`dni` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Clientes de la empresa';


-- -----------------------------------------------------
-- Table `pawpro_database`.`Roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`Roles` (
  `rol_id` INT NOT NULL AUTO_INCREMENT,
  `nombre_rol` VARCHAR(50) NOT NULL COMMENT 'Ej: Gerente General, Encargado Almacen, Encargado Ventas',
  `descripcion` TEXT NULL DEFAULT NULL,
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rol_id`),
  UNIQUE INDEX `nombre_rol` (`nombre_rol` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Roles de usuario para control de acceso';


-- -----------------------------------------------------
-- Table `pawpro_database`.`Usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`Usuarios` (
  `usuario_id` INT NOT NULL AUTO_INCREMENT,
  `nombre_usuario` VARCHAR(100) NOT NULL,
  `nombre_acceso` VARCHAR(50) NOT NULL COMMENT 'Login name',
  `contraseña_hash` VARCHAR(255) NOT NULL COMMENT 'Almacenar hash de la contraseña',
  `correo_electronico` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Para notificaciones, etc.',
  `rol_id` INT NOT NULL,
  `estado` ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`usuario_id`),
  UNIQUE INDEX `nombre_acceso` (`nombre_acceso` ASC) VISIBLE,
  UNIQUE INDEX `correo_electronico` (`correo_electronico` ASC) VISIBLE,
  INDEX `idx_rol` (`rol_id` ASC) VISIBLE,
  CONSTRAINT `usuarios_ibfk_1`
    FOREIGN KEY (`rol_id`)
    REFERENCES `pawpro_database`.`Roles` (`rol_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Usuarios del sistema';


-- -----------------------------------------------------
-- Table `pawpro_database`.`BoletasVenta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`BoletasVenta` (
  `boleta_venta_id` INT NOT NULL AUTO_INCREMENT,
  `numero_boleta` VARCHAR(50) NOT NULL COMMENT 'Número correlativo de la boleta',
  `cliente_id` INT NULL DEFAULT NULL COMMENT 'Cliente asociado (puede ser nulo para ventas genéricas)',
  `fecha_venta` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` INT NOT NULL COMMENT 'Usuario que registró la venta',
  `monto_total` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `estado` ENUM('emitida', 'anulada') NOT NULL DEFAULT 'emitida',
  `observaciones` TEXT NULL DEFAULT NULL,
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`boleta_venta_id`),
  UNIQUE INDEX `numero_boleta` (`numero_boleta` ASC) VISIBLE,
  INDEX `idx_cliente` (`cliente_id` ASC) VISIBLE,
  INDEX `idx_usuario` (`usuario_id` ASC) VISIBLE,
  INDEX `idx_fecha_venta` (`fecha_venta` ASC) VISIBLE,
  CONSTRAINT `boletasventa_ibfk_1`
    FOREIGN KEY (`cliente_id`)
    REFERENCES `pawpro_database`.`Clientes` (`cliente_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `boletasventa_ibfk_2`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `pawpro_database`.`Usuarios` (`usuario_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Registro de boletas de venta';


-- -----------------------------------------------------
-- Table `pawpro_database`.`Marcas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`Marcas` (
  `marca_id` INT NOT NULL AUTO_INCREMENT,
  `codigo_marca` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Código opcional para la marca',
  `nombre_marca` VARCHAR(100) NOT NULL,
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`marca_id`),
  UNIQUE INDEX `nombre_marca` (`nombre_marca` ASC) VISIBLE,
  UNIQUE INDEX `codigo_marca` (`codigo_marca` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Marcas de los productos comercializados';

-- -----------------------------------------------------
-- Table `pawpro_database`.`Categoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`Categoria` (
  `id_categoria` INT NOT NULL AUTO_INCREMENT,
  `nombre_categoria` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NULL DEFAULT NULL,
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_categoria`),
  UNIQUE INDEX `nombre_categoria_UNIQUE` (`nombre_categoria` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Categorías de productos (ej: Medicamentos, Alimentos)';

-- -----------------------------------------------------
-- Table `pawpro_database`.`Subcategoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`Subcategoria` (
  `id_subcategoria` INT NOT NULL AUTO_INCREMENT,
  `id_categoria` INT NOT NULL,
  `nombre_subcategoria` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NULL DEFAULT NULL,
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_subcategoria`),
  UNIQUE INDEX `uq_subcategoria_categoria_nombre` (`id_categoria` ASC, `nombre_subcategoria` ASC) VISIBLE,
  INDEX `fk_subcategoria_categoria_idx` (`id_categoria` ASC) VISIBLE,
  CONSTRAINT `fk_subcategoria_categoria`
    FOREIGN KEY (`id_categoria`)
    REFERENCES `pawpro_database`.`Categoria` (`id_categoria`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Subcategorías de productos (ej: Antibióticos, Alimento Seco)';

-- -----------------------------------------------------
-- Table `pawpro_database`.`Presentacion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`Presentacion` (
  `id_presentacion` INT NOT NULL AUTO_INCREMENT,
  `nombre_presentacion` VARCHAR(100) NOT NULL,
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_presentacion`),
  UNIQUE INDEX `nombre_presentacion_UNIQUE` (`nombre_presentacion` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Tipos de presentación de los productos (ej: Caja, Frasco, Bolsa)';


-- -----------------------------------------------------
-- Table `pawpro_database`.`Productos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`Productos` (
  `producto_id` INT NOT NULL AUTO_INCREMENT,
  `codigo_producto` VARCHAR(50) NOT NULL COMMENT 'Código único legible del producto',
  `nombre_producto` VARCHAR(255) NOT NULL,
  `descripcion` TEXT NULL DEFAULT NULL,
  `marca_id` INT NOT NULL,
  `subcategoria_id` INT NOT NULL,
  `presentacion_id` INT NOT NULL,
  `precio_venta_unitario` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `stock_minimo` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Umbral para alerta de bajo stock',
  `stock_maximo` INT UNSIGNED NULL DEFAULT NULL COMMENT 'Opcional, para control de sobre-stock',
  `unidad_medida` VARCHAR(20) NULL DEFAULT 'unidad' COMMENT 'Ej: unidad, caja, kg, lt',
  `estado` ENUM('activo', 'descontinuado', 'suspendido') NOT NULL DEFAULT 'activo',
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`producto_id`),
  UNIQUE INDEX `codigo_producto` (`codigo_producto` ASC) VISIBLE,
  INDEX `idx_marca` (`marca_id` ASC) VISIBLE,
  INDEX `idx_subcategoria` (`subcategoria_id` ASC) VISIBLE,
  INDEX `idx_presentacion` (`presentacion_id` ASC) VISIBLE,
  INDEX `idx_nombre_producto` (`nombre_producto` ASC) VISIBLE,
  CONSTRAINT `productos_ibfk_1`
    FOREIGN KEY (`marca_id`)
    REFERENCES `pawpro_database`.`Marcas` (`marca_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_productos_subcategoria`
    FOREIGN KEY (`subcategoria_id`)
    REFERENCES `pawpro_database`.`Subcategoria` (`id_subcategoria`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_productos_presentacion`
    FOREIGN KEY (`presentacion_id`)
    REFERENCES `pawpro_database`.`Presentacion` (`id_presentacion`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Catálogo de productos';


-- -----------------------------------------------------
-- Table `pawpro_database`.`DetallesBoletaVenta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`DetallesBoletaVenta` (
  `detalle_venta_id` INT NOT NULL AUTO_INCREMENT,
  `boleta_venta_id` INT NOT NULL,
  `producto_id` INT NOT NULL,
  `cantidad_vendida` INT UNSIGNED NOT NULL,
  `precio_venta_unitario_boleta` DECIMAL(10,2) NOT NULL COMMENT 'Precio al momento de la venta',
  `subtotal` DECIMAL(12,2) NOT NULL COMMENT 'cantidad * precio_unitario',
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`detalle_venta_id`),
  INDEX `idx_boleta_venta` (`boleta_venta_id` ASC) VISIBLE,
  INDEX `idx_producto` (`producto_id` ASC) VISIBLE,
  CONSTRAINT `detallesboletaventa_ibfk_1`
    FOREIGN KEY (`boleta_venta_id`)
    REFERENCES `pawpro_database`.`BoletasVenta` (`boleta_venta_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `detallesboletaventa_ibfk_2`
    FOREIGN KEY (`producto_id`)
    REFERENCES `pawpro_database`.`Productos` (`producto_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Items de cada boleta de venta';


-- -----------------------------------------------------
-- Table `pawpro_database`.`Proveedores`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`Proveedores` (
  `proveedor_id` INT NOT NULL AUTO_INCREMENT,
  `ruc` VARCHAR(11) NOT NULL COMMENT 'RUC del proveedor',
  `razon_social` VARCHAR(255) NOT NULL,
  `ciudad` VARCHAR(100) NULL DEFAULT NULL,
  `direccion` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Campo adicional útil',
  `telefono_representante` VARCHAR(20) NULL DEFAULT NULL,
  `correo_electronico` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Campo adicional útil',
  `productos_distribuidos` TEXT NULL DEFAULT NULL COMMENT 'Marcas/Tipos que distribuye (simple, mejorar si es M:N)',
  `estado` ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`proveedor_id`),
  UNIQUE INDEX `ruc` (`ruc` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Proveedores de productos';


-- -----------------------------------------------------
-- Table `pawpro_database`.`FacturasCompra`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`FacturasCompra` (
  `factura_compra_id` INT NOT NULL AUTO_INCREMENT,
  `numero_factura` VARCHAR(50) NOT NULL COMMENT 'Número físico de la factura del proveedor',
  `proveedor_id` INT NOT NULL,
  `fecha_compra` DATE NOT NULL,
  `fecha_registro` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` INT NOT NULL COMMENT 'Usuario que registró la factura',
  `monto_total` DECIMAL(12,2) NULL DEFAULT NULL COMMENT 'Calculado o ingresado',
  `estado` ENUM('registrada', 'pagada', 'anulada') NOT NULL DEFAULT 'registrada',
  `observaciones` TEXT NULL DEFAULT NULL,
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`factura_compra_id`),
  UNIQUE INDEX `uq_proveedor_numero_factura` (`proveedor_id` ASC, `numero_factura` ASC) VISIBLE,
  INDEX `idx_proveedor` (`proveedor_id` ASC) VISIBLE,
  INDEX `idx_usuario` (`usuario_id` ASC) VISIBLE,
  INDEX `idx_fecha_compra` (`fecha_compra` ASC) VISIBLE,
  CONSTRAINT `facturascompra_ibfk_1`
    FOREIGN KEY (`proveedor_id`)
    REFERENCES `pawpro_database`.`Proveedores` (`proveedor_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `facturascompra_ibfk_2`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `pawpro_database`.`Usuarios` (`usuario_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Registro de facturas de compra';


-- -----------------------------------------------------
-- Table `pawpro_database`.`LotesProducto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`LotesProducto` (
  `lote_id` INT NOT NULL AUTO_INCREMENT,
  `producto_id` INT NOT NULL,
  `codigo_lote` VARCHAR(100) NOT NULL COMMENT 'Identificador único del lote para este producto',
  `cantidad_actual` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Stock disponible de este lote',
  `fecha_vencimiento` DATE NOT NULL,
  `precio_compra_unitario` DECIMAL(10,2) NOT NULL COMMENT 'Precio al que se compró este lote',
  `fecha_ingreso` DATE NOT NULL COMMENT 'Fecha en que ingresó este lote al almacén',
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`lote_id`),
  UNIQUE INDEX `uq_producto_codigo_lote` (`producto_id` ASC, `codigo_lote` ASC) VISIBLE,
  INDEX `idx_producto` (`producto_id` ASC) VISIBLE,
  INDEX `idx_fecha_vencimiento` (`fecha_vencimiento` ASC) VISIBLE,
  CONSTRAINT `lotesproducto_ibfk_1`
    FOREIGN KEY (`producto_id`)
    REFERENCES `pawpro_database`.`Productos` (`producto_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Lotes específicos de productos en inventario';


-- -----------------------------------------------------
-- Table `pawpro_database`.`DetallesFacturaCompra`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`DetallesFacturaCompra` (
  `detalle_factura_id` INT NOT NULL AUTO_INCREMENT,
  `factura_compra_id` INT NOT NULL,
  `lote_id` INT NOT NULL COMMENT 'Lote que se crea o actualiza con esta compra',
  `producto_id` INT NOT NULL COMMENT 'Referencia informativa al producto',
  `cantidad_comprada` INT UNSIGNED NOT NULL,
  `precio_compra_unitario_factura` DECIMAL(10,2) NOT NULL COMMENT 'Precio unitario según esta factura específica',
  `subtotal` DECIMAL(12,2) NOT NULL COMMENT 'cantidad * precio_unitario',
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`detalle_factura_id`),
  INDEX `idx_factura_compra` (`factura_compra_id` ASC) VISIBLE,
  INDEX `idx_lote` (`lote_id` ASC) VISIBLE,
  INDEX `idx_producto` (`producto_id` ASC) VISIBLE,
  CONSTRAINT `detallesfacturacompra_ibfk_1`
    FOREIGN KEY (`factura_compra_id`)
    REFERENCES `pawpro_database`.`FacturasCompra` (`factura_compra_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `detallesfacturacompra_ibfk_2`
    FOREIGN KEY (`lote_id`)
    REFERENCES `pawpro_database`.`LotesProducto` (`lote_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `detallesfacturacompra_ibfk_3`
    FOREIGN KEY (`producto_id`)
    REFERENCES `pawpro_database`.`Productos` (`producto_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Items de cada factura de compra, vinculados a lotes';


-- -----------------------------------------------------
-- Table `pawpro_database`.`NotasBaja`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pawpro_database`.`NotasBaja` (
  `nota_baja_id` INT NOT NULL AUTO_INCREMENT,
  `lote_id` INT NOT NULL COMMENT 'Lote específico afectado por la baja',
  `producto_id` INT NOT NULL COMMENT 'Referencia informativa al producto',
  `motivo_baja` VARCHAR(255) NOT NULL COMMENT 'Ej: Vencido, Deteriorado, Muestra, Ajuste',
  `cantidad_baja` INT UNSIGNED NOT NULL,
  `fecha_baja` DATE NOT NULL,
  `usuario_id` INT NOT NULL COMMENT 'Usuario que registró la baja',
  `observaciones` TEXT NULL DEFAULT NULL,
  `fecha_creacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`nota_baja_id`),
  INDEX `idx_lote` (`lote_id` ASC) VISIBLE,
  INDEX `idx_producto` (`producto_id` ASC) VISIBLE,
  INDEX `idx_usuario` (`usuario_id` ASC) VISIBLE,
  INDEX `idx_fecha_baja` (`fecha_baja` ASC) VISIBLE,
  CONSTRAINT `notasbaja_ibfk_1`
    FOREIGN KEY (`lote_id`)
    REFERENCES `pawpro_database`.`LotesProducto` (`lote_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `notasbaja_ibfk_2`
    FOREIGN KEY (`producto_id`)
    REFERENCES `pawpro_database`.`Productos` (`producto_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `notasbaja_ibfk_3`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `pawpro_database`.`Usuarios` (`usuario_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci
COMMENT = 'Registro de bajas de inventario';


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
