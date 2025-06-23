USE `pawpro_database`;

-- Deshabilitar temporalmente las verificaciones de clave foránea para una limpieza limpia
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar datos existentes en orden inverso de dependencia para evitar errores de clave foránea
DELETE FROM `DetallesFacturaCompra`;
DELETE FROM `FacturasCompra`;
DELETE FROM `NotasBaja`;
DELETE FROM `LotesProducto`;
DELETE FROM `DetallesBoletaVenta`;
DELETE FROM `BoletasVenta`;
DELETE FROM `Productos`;
DELETE FROM `Presentacion`;
DELETE FROM `Subcategoria`;
DELETE FROM `Categoria`;
DELETE FROM `Marcas`;
DELETE FROM `Proveedores`;
DELETE FROM `Usuarios`;
DELETE FROM `Clientes`;
DELETE FROM `Roles`;

-- Habilitar nuevamente las verificaciones de clave foránea
SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------
-- Insertar Roles
-- -----------------------------------------------------
INSERT INTO `Roles` (`nombre_rol`, `descripcion`) VALUES
('Administrador', 'El rol general que tiene todos los privilegios'),
('Encargado Almacen', 'Gestiona inventario, lotes, y notas de baja.'),
('Encargado de ventas', 'El rol para los trabajadores de ventas');

-- -----------------------------------------------------
-- Insertar Clientes
-- -----------------------------------------------------
INSERT INTO `Clientes` (`dni`, `nombre_cliente`, `direccion`, `telefono`, `correo_electronico`) VALUES
('12345678', 'Ana Torres', 'Av. Las Palmeras 123', '987654321', 'ana.torres@example.com'),
('87654321', 'Luis Mendoza', 'Jr. Los Álamos 456', '912345678', 'luis.mendoza@example.com'),
('99887766', 'Sofía Vargas', 'Calle Los Jazmines 789', '955443322', 'sofia.vargas@example.com'),
('11223344', 'Diego Salas', 'Av. San Martín 321', '966778899', 'diego.salas@example.com');

-- -----------------------------------------------------
-- Insertar Usuarios
-- -----------------------------------------------------
-- IMPORTANTE: En una aplicación real, 'contraseña_hash' debe almacenar una contraseña segura con hash, no texto plano.
INSERT INTO `Usuarios` (`nombre_usuario`, `nombre_acceso`, `contraseña_hash`, `correo_electronico`, `rol_id`, `estado`) VALUES
('Juan Pérez', 'jperez', 'hashed_password_123', 'juan.perez@example.com', (SELECT rol_id FROM `Roles` WHERE `nombre_rol` = 'Administrador'), 'activo'),
('María López', 'mlopez', 'hashed_password_456', 'maria.lopez@example.com', (SELECT rol_id FROM `Roles` WHERE `nombre_rol` = 'Encargado Almacen'), 'activo'),
('Carlos García', 'cgarcia', 'hashed_password_789', 'carlos.garcia@example.com', (SELECT rol_id FROM `Roles` WHERE `nombre_rol` = 'Encargado de ventas'), 'activo'),
('Admin Principal', 'admin', 'contraseña_segura_hash', 'admin@pawpro.vet', (SELECT rol_id FROM `Roles` WHERE `nombre_rol` = 'Administrador'), 'activo'),
('Juan Perez Almacen', 'jalmacen', 'otra_contraseña_hash', 'jalmacen@pawpro.vet', (SELECT rol_id FROM `Roles` WHERE `nombre_rol` = 'Encargado Almacen'), 'activo'),
('Laura Flores', 'lflores', 'hashed_password_abc', 'laura.flores@example.com', (SELECT rol_id FROM `Roles` WHERE `nombre_rol` = 'Encargado de ventas'), 'activo');


-- -----------------------------------------------------
-- Insertar Marcas
-- -----------------------------------------------------
INSERT INTO `Marcas` (`nombre_marca`, `codigo_marca`) VALUES
('Purina', 'PUR001'),
('Royal Canin', 'RCAN002'),
('Pedigree', 'PDG003'),
('Bayer', 'BAY01'),
('Zoetis', 'ZOE01'),
('MSD Salud Animal', 'MSD01'),
('VetScience', 'VTS004'),
('PetCare', 'PTC005');

-- -----------------------------------------------------
-- Insertar Categorias
-- -----------------------------------------------------
INSERT INTO `Categoria` (`nombre_categoria`, `descripcion`) VALUES
('Medicamentos','Medicamentos utilizado para los animales'),
('Suplementos', 'Suplementos utilizados para animales'),
('Antipulgas', 'Las mejores antipulgas para tu mascota'),
('Alimentos', 'Alimentos balanceados para la salud de tu mascota'),
('Accesorios', 'Productos no directamente medicinales o alimenticios para mascotas.'),
('Higiene', 'Productos para el cuidado e higiene de mascotas.'); -- Nueva Categoría

-- -----------------------------------------------------
-- Insertar Subcategorias
-- -----------------------------------------------------
INSERT INTO `Subcategoria` (`id_categoria`, `nombre_subcategoria`, `descripcion`) VALUES
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Anestesicos", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Antianemicos", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Antibioticos", 'Medicamentos para tratar infecciones bacterianas'),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Antiinflamatorios", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Hormonales", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Cicatrizantes", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Vitaminas", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Antiparasitarios", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Antiemeticos", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Analgesicos", 'Medicamentos para aliviar el dolor'),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Protector gastrico", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Tranquilizantes", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Articulaciones", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "General", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos'), "Regenerador osteoarticular", NULL),

((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Suplementos'), "Leche materna", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Suplementos'), "Vitaminas", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Suplementos'), "Tranquilizantes", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Suplementos'), "Renales", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Suplementos'), "Piel y pelo", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Suplementos'), "Laxantes", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Suplementos'), "Hepatoprotector", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Suplementos'), "Articulaciones", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Suplementos'), "Antiparasitarios", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Suplementos'), "Antianemicos", NULL),

((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Antipulgas'), "Spray", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Antipulgas'), "Pipetas", 'Soluciones tópicas para el control de pulgas y garrapatas'),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Antipulgas'), "Pastillas", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Antipulgas'), "Collares", NULL),

((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Alimentos'), "Alimentos medicados", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Alimentos'), "Alimentos no medicados", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Alimentos'), "Sazonador", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Alimentos'), "Premios/Snack", NULL),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Alimentos'), "Alimento Humedo", 'Latas y pouches de alimento húmedo para gatos'),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Alimentos'), "Alimento Barf", NULL),

((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Accesorios'), "Juguetes", 'Artículos para el entretenimiento de mascotas.'),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Accesorios'), "Correas", 'Correas y arneses para pasear mascotas.'),
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Higiene'), "Shampoo", 'Champús y acondicionadores para el baño de mascotas.'), -- Nueva Subcategoría
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Higiene'), "Cepillos", 'Cepillos para el pelo y la higiene dental.'); -- Nueva Subcategoría


-- -----------------------------------------------------
-- Insertar Presentaciones
-- -----------------------------------------------------
INSERT INTO `Presentacion` (`nombre_presentacion`) VALUES
('Caja con 10 Tabletas'),
('Frasco Gotero 10ml'),
('Bolsa 3kg'),
('Pipeta individual'),
('unidad'),
('Envase 500ml'), -- Nueva presentación
('Paquete'); -- Nueva presentación

-- -----------------------------------------------------
-- Insertar Productos
-- -----------------------------------------------------
-- Producto 1: Antibiótico
INSERT INTO `Productos` (
    `codigo_producto`, `nombre_producto`, `descripcion`, `marca_id`, `subcategoria_id`, `presentacion_id`,
    `precio_venta_unitario`, `stock_minimo`, `stock_maximo`, `unidad_medida`, `estado`
) VALUES (
    'PROD001', 'Amoxipet 250mg', 'Amoxicilina para perros y gatos. Tratamiento de infecciones.',
    (SELECT marca_id FROM `Marcas` WHERE `nombre_marca` = 'Bayer'),
    (SELECT id_subcategoria FROM `Subcategoria` WHERE `nombre_subcategoria` = 'Antibioticos' AND `id_categoria` = (SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos')),
    (SELECT id_presentacion FROM `Presentacion` WHERE `nombre_presentacion` = 'Caja con 10 Tabletas'),
    25.50,
    10,
    50,
    'unidad',
    'activo'
);

-- Producto 2: Alimento para perro
INSERT INTO `Productos` (
    `codigo_producto`, `nombre_producto`, `descripcion`, `marca_id`, `subcategoria_id`, `presentacion_id`,
    `precio_venta_unitario`, `stock_minimo`, `stock_maximo`, `unidad_medida`, `estado`
) VALUES (
    'PROD002', 'Royal Canin Maxi Adulto', 'Alimento balanceado para perros adultos de raza grande.',
    (SELECT marca_id FROM `Marcas` WHERE `nombre_marca` = 'Royal Canin'),
    (SELECT id_subcategoria FROM `Subcategoria` WHERE `nombre_subcategoria` = 'Alimentos no medicados' AND `id_categoria` = (SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Alimentos')),
    (SELECT id_presentacion FROM `Presentacion` WHERE `nombre_presentacion` = 'Bolsa 3kg'),
    180.00,
    5,
    30,
    'bolsa',
    'activo'
);

-- Producto 3: Antipulgas en Pipeta
INSERT INTO `Productos` (
    `codigo_producto`, `nombre_producto`, `descripcion`, `marca_id`, `subcategoria_id`, `presentacion_id`,
    `precio_venta_unitario`, `stock_minimo`, `stock_maximo`, `unidad_medida`, `estado`
) VALUES (
    'PROD003', 'Frontline Plus Perros M', 'Pipeta antipulgas y garrapatas para perros medianos (10-20kg).',
    (SELECT marca_id FROM `Marcas` WHERE `nombre_marca` = 'MSD Salud Animal'),
    (SELECT id_subcategoria FROM `Subcategoria` WHERE `nombre_subcategoria` = 'Pipetas' AND `id_categoria` = (SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Antipulgas')),
    (SELECT id_presentacion FROM `Presentacion` WHERE `nombre_presentacion` = 'Pipeta individual'),
    45.00,
    20,
    100,
    'unidad',
    'activo'
);

-- Producto 4: Alimento Húmedo Gato
INSERT INTO `Productos` (
    `codigo_producto`, `nombre_producto`, `descripcion`, `marca_id`, `subcategoria_id`, `presentacion_id`,
    `precio_venta_unitario`, `stock_minimo`, `stock_maximo`, `unidad_medida`, `estado`
) VALUES (
    'ALIG002', 'Alimento Húmedo Gato Pouch 85g', 'Pouch de alimento húmedo para gatos adultos sabor salmón',
    (SELECT marca_id FROM `Marcas` WHERE `nombre_marca` = 'Royal Canin'),
    (SELECT id_subcategoria FROM `Subcategoria` WHERE `nombre_subcategoria` = 'Alimento Humedo' AND `id_categoria` = (SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Alimentos')),
    (SELECT id_presentacion FROM `Presentacion` WHERE `nombre_presentacion` = 'unidad'),
    4.25,
    50,
    NULL,
    'unidad',
    'activo'
);

-- Producto 5: Juguete
INSERT INTO `Productos` (
    `codigo_producto`, `nombre_producto`, `descripcion`, `marca_id`, `subcategoria_id`, `presentacion_id`,
    `precio_venta_unitario`, `stock_minimo`, `stock_maximo`, `unidad_medida`, `estado`
) VALUES (
    'JUGT001', 'Juguete Pelota Duradera', 'Pelota de caucho resistente para perros grandes',
    (SELECT marca_id FROM `Marcas` WHERE `nombre_marca` = 'Pedigree'),
    (SELECT id_subcategoria FROM `Subcategoria` WHERE `nombre_subcategoria` = 'Juguetes' AND `id_categoria` = (SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Accesorios')),
    (SELECT id_presentacion FROM `Presentacion` WHERE `nombre_presentacion` = 'unidad'),
    15.00,
    5,
    NULL,
    'unidad',
    'activo'
);

-- Producto 6: Collar Antipulgas
INSERT INTO `Productos` (
    `codigo_producto`, `nombre_producto`, `descripcion`, `marca_id`, `subcategoria_id`, `presentacion_id`,
    `precio_venta_unitario`, `stock_minimo`, `stock_maximo`, `unidad_medida`, `estado`
) VALUES (
    'ANTIP001', 'Seresto Collar Antipulgas Gatos', 'Collar para protección contra pulgas y garrapatas en gatos por 8 meses.',
    (SELECT marca_id FROM `Marcas` WHERE `nombre_marca` = 'Bayer'),
    (SELECT id_subcategoria FROM `Subcategoria` WHERE `nombre_subcategoria` = 'Collares' AND `id_categoria` = (SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Antipulgas')),
    (SELECT id_presentacion FROM `Presentacion` WHERE `nombre_presentacion` = 'unidad'),
    75.00,
    10,
    40,
    'unidad',
    'activo'
);

-- Producto 7: Shampoo para Mascotas
INSERT INTO `Productos` (
    `codigo_producto`, `nombre_producto`, `descripcion`, `marca_id`, `subcategoria_id`, `presentacion_id`,
    `precio_venta_unitario`, `stock_minimo`, `stock_maximo`, `unidad_medida`, `estado`
) VALUES (
    'HIG001', 'Shampoo Hipoalergénico Perros', 'Champú suave para perros con piel sensible, sin fragancias fuertes.',
    (SELECT marca_id FROM `Marcas` WHERE `nombre_marca` = 'PetCare'),
    (SELECT id_subcategoria FROM `Subcategoria` WHERE `nombre_subcategoria` = 'Shampoo' AND `id_categoria` = (SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Higiene')),
    (SELECT id_presentacion FROM `Presentacion` WHERE `nombre_presentacion` = 'Envase 500ml'),
    30.00,
    15,
    60,
    'unidad',
    'activo'
);


-- -----------------------------------------------------
-- Insertar Lotes de Productos (Stock Inicial)
-- -----------------------------------------------------
-- Lote para Producto 1 (Amoxipet 250mg)
INSERT INTO `LotesProducto` (
    `producto_id`, `codigo_lote`, `cantidad_actual`, `fecha_vencimiento`, `precio_compra_unitario`, `fecha_ingreso`
) VALUES (
    (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD001'), 'LOTE-AMX001', 30, '2026-12-31', 15.00, '2025-05-19'
);
INSERT INTO `LotesProducto` (
    `producto_id`, `codigo_lote`, `cantidad_actual`, `fecha_vencimiento`, `precio_compra_unitario`, `fecha_ingreso`
) VALUES (
    (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD001'), 'LOTE-AMX002', 20, '2027-03-31', 15.50, '2025-05-19'
);

-- Lote para Producto 2 (Royal Canin Maxi Adulto)
INSERT INTO `LotesProducto` (
    `producto_id`, `codigo_lote`, `cantidad_actual`, `fecha_vencimiento`, `precio_compra_unitario`, `fecha_ingreso`
) VALUES (
    (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD002'), 'LOTE-RCM001', 20, '2026-10-01', 120.00, '2025-05-19'
);

-- Lote para Producto 3 (Frontline Plus Perros M)
INSERT INTO `LotesProducto` (
    `producto_id`, `codigo_lote`, `cantidad_actual`, `fecha_vencimiento`, `precio_compra_unitario`, `fecha_ingreso`
) VALUES (
    (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD003'), 'LOTE-FRP001', 50, '2027-01-15', 28.00, '2025-05-19'
);

-- Lote para Producto 4 (Alimento Húmedo Gato Pouch 85g)
INSERT INTO `LotesProducto` (
    `producto_id`, `codigo_lote`, `cantidad_actual`, `fecha_vencimiento`, `precio_compra_unitario`, `fecha_ingreso`
) VALUES (
    (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'ALIG002'), 'LOTE-GAT001', 40, '2026-08-01', 2.50, '2025-05-20'
);

-- Lote para Producto 5 (Juguete Pelota Duradera)
INSERT INTO `LotesProducto` (
    `producto_id`, `codigo_lote`, `cantidad_actual`, `fecha_vencimiento`, `precio_compra_unitario`, `fecha_ingreso`
) VALUES (
    (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'JUGT001'), 'LOTE-JUG001', 15, '2030-01-01', 9.00, '2025-05-20'
);

-- Lote para Producto 6 (Collar Antipulgas)
INSERT INTO `LotesProducto` (
    `producto_id`, `codigo_lote`, `cantidad_actual`, `fecha_vencimiento`, `precio_compra_unitario`, `fecha_ingreso`
) VALUES (
    (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'ANTIP001'), 'LOTE-COLL001', 25, '2028-06-30', 50.00, '2025-06-01'
);

-- Lote para Producto 7 (Shampoo Hipoalergénico)
INSERT INTO `LotesProducto` (
    `producto_id`, `codigo_lote`, `cantidad_actual`, `fecha_vencimiento`, `precio_compra_unitario`, `fecha_ingreso`
) VALUES (
    (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'HIG001'), 'LOTE-SHP001', 35, '2027-09-15', 18.00, '2025-06-02'
);

-- -----------------------------------------------------
-- Insertar Proveedores
-- -----------------------------------------------------
INSERT INTO `Proveedores` (`ruc`, `razon_social`, `ciudad`, `direccion`, `telefono_representante`, `correo_electronico`, `productos_distribuidos`, `estado`) VALUES
('20100062029', 'DISTRIBUIDORA VETERINARIA DEL NORTE S.A.C.', 'Trujillo', 'Av. España 1234, Centro Cívico', '044-200100', 'ventas@divetnorte.com.pe', 'Medicamentos veterinarios, alimentos balanceados, accesorios para mascotas.', 'activo'),
('20506070801', 'AGROVET MARKET ANIMAL HEALTH', 'Lima', 'Av. El Derby 254, Santiago de Surco', '01-6107900', 'contacto@agrovetmarket.com', 'Productos farmacéuticos y nutricionales para uso veterinario.', 'activo'),
('10458877221', 'IMPORTACIONES VET PERÚ E.I.R.L.', 'Arequipa', 'Calle Mercaderes 456, Cercado', '054-280510', 'info@vetperuimport.com', 'Equipamiento médico veterinario, insumos quirúrgicos.', 'activo'),
('20304050607', 'PET FOODS DEL PERÚ S.R.L.', 'Lima', 'Calle Los Alamos 321, Miraflores', '01-4478899', 'pedidos@petfoods.com.pe', 'Alimentos premium para perros y gatos, snacks.', 'inactivo'),
('20987654321', 'SUMINISTROS VETERINARIOS S.A.C.', 'Chiclayo', 'Av. Balta 789', '074-234567', 'ventas@suministrosvet.com', 'Medicamentos, vacunas y material quirúrgico.', 'activo'),
('20123456789', 'ALIMENTOS MASCOTAS PERÚ S.A.C.', 'Lima', 'Calle La Paz 101, Barranco', '01-7890123', 'info@alimentosmascotasperu.com', 'Variedad de alimentos secos y húmedos para mascotas.', 'activo');

-- -----------------------------------------------------
-- Insertar FacturasCompra
-- -----------------------------------------------------
INSERT INTO `FacturasCompra` (`numero_factura`, `proveedor_id`, `fecha_compra`, `usuario_id`, `monto_total`, `estado`, `observaciones`) VALUES
('FACC001-2025-05', (SELECT proveedor_id FROM `Proveedores` WHERE `ruc` = '20100062029'), '2025-05-25', (SELECT usuario_id FROM `Usuarios` WHERE `nombre_acceso` = 'jalmacen'), 500.00, 'registrada', 'Compra de reposición de stock'),
('FACC002-2025-06', (SELECT proveedor_id FROM `Proveedores` WHERE `ruc` = '20506070801'), '2025-06-01', (SELECT usuario_id FROM `Usuarios` WHERE `nombre_acceso` = 'jalmacen'), 350.00, 'registrada', 'Compra mensual de suplementos'),
('FACC003-2025-06', (SELECT proveedor_id FROM `Proveedores` WHERE `ruc` = '20987654321'), '2025-06-05', (SELECT usuario_id FROM `Usuarios` WHERE `nombre_acceso` = 'jalmacen'), 1200.00, 'pagada', 'Factura por compra de nuevo lote de antipulgas y shampoo.'),
('FACC004-2025-06', (SELECT proveedor_id FROM `Proveedores` WHERE `ruc` = '20123456789'), '2025-06-07', (SELECT usuario_id FROM `Usuarios` WHERE `nombre_acceso` = 'jalmacen'), 750.00, 'registrada', 'Compra de alimentos variados para mascotas.');


-- -----------------------------------------------------
-- Insertar DetallesFacturaCompra
-- -----------------------------------------------------
INSERT INTO `DetallesFacturaCompra` (`factura_compra_id`, `lote_id`, `producto_id`, `cantidad_comprada`, `precio_compra_unitario_factura`, `subtotal`) VALUES
((SELECT factura_compra_id FROM `FacturasCompra` WHERE `numero_factura` = 'FACC001-2025-05'), (SELECT lote_id FROM `LotesProducto` WHERE `codigo_lote` = 'LOTE-AMX001'), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD001'), 20, 15.00, 300.00),
((SELECT factura_compra_id FROM `FacturasCompra` WHERE `numero_factura` = 'FACC001-2025-05'), (SELECT lote_id FROM `LotesProducto` WHERE `codigo_lote` = 'LOTE-RCM001'), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD002'), 10, 120.00, 1200.00), -- Subtotal ajustado para 10 unidades
((SELECT factura_compra_id FROM `FacturasCompra` WHERE `numero_factura` = 'FACC002-2025-06'), (SELECT lote_id FROM `LotesProducto` WHERE `codigo_lote` = 'LOTE-GAT001'), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'ALIG002'), 30, 2.50, 75.00),
((SELECT factura_compra_id FROM `FacturasCompra` WHERE `numero_factura` = 'FACC003-2025-06'), (SELECT lote_id FROM `LotesProducto` WHERE `codigo_lote` = 'LOTE-COLL001'), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'ANTIP001'), 25, 50.00, 1250.00),
((SELECT factura_compra_id FROM `FacturasCompra` WHERE `numero_factura` = 'FACC003-2025-06'), (SELECT lote_id FROM `LotesProducto` WHERE `codigo_lote` = 'LOTE-SHP001'), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'HIG001'), 35, 18.00, 630.00),
((SELECT factura_compra_id FROM `FacturasCompra` WHERE `numero_factura` = 'FACC004-2025-06'), (SELECT lote_id FROM `LotesProducto` WHERE `codigo_lote` = 'LOTE-RCM001'), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD002'), 5, 120.00, 600.00);

-- -----------------------------------------------------
-- Insertar BoletasVenta
-- -----------------------------------------------------
INSERT INTO `BoletasVenta` (`numero_boleta`, `cliente_id`, `fecha_venta`, `usuario_id`, `monto_total`, `estado`, `observaciones`) VALUES
('BV001-2025-06-01', (SELECT cliente_id FROM `Clientes` WHERE `dni` = '12345678'), '2025-06-01 10:00:00', (SELECT usuario_id FROM `Usuarios` WHERE `nombre_acceso` = 'cgarcia'), 150.00, 'emitida', 'Venta a cliente Ana Torres'),
('BV002-2025-06-01', (SELECT cliente_id FROM `Clientes` WHERE `dni` = '87654321'), '2025-06-01 11:30:00', (SELECT usuario_id FROM `Usuarios` WHERE `nombre_acceso` = 'cgarcia'), 45.00, 'emitida', 'Venta a cliente Luis Mendoza'),
('BV003-2025-06-02', (SELECT cliente_id FROM `Clientes` WHERE `dni` = '99887766'), '2025-06-02 09:15:00', (SELECT usuario_id FROM `Usuarios` WHERE `nombre_acceso` = 'lflores'), 250.00, 'emitida', 'Venta de alimento y suplemento para Sofía Vargas.'),
('BV004-2025-06-03', NULL, '2025-06-03 14:45:00', (SELECT usuario_id FROM `Usuarios` WHERE `nombre_acceso` = 'cgarcia'), 75.00, 'emitida', 'Venta genérica de juguetes.');

-- -----------------------------------------------------
-- Insertar DetallesBoletaVenta
-- -----------------------------------------------------
INSERT INTO `DetallesBoletaVenta` (`boleta_venta_id`, `producto_id`, `cantidad_vendida`, `precio_venta_unitario_boleta`, `subtotal`) VALUES
((SELECT boleta_venta_id FROM `BoletasVenta` WHERE `numero_boleta` = 'BV001-2025-06-01'), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD001'), 2, 25.50, 51.00),
((SELECT boleta_venta_id FROM `BoletasVenta` WHERE `numero_boleta` = 'BV001-2025-06-01'), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD002'), 1, 180.00, 180.00),
((SELECT boleta_venta_id FROM `BoletasVenta` WHERE `numero_boleta` = 'BV001-2025-06-01'), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD003'), 1, 45.00, 45.00),
((SELECT boleta_venta_id FROM `BoletasVenta` WHERE `numero_boleta` = 'BV002-2025-06-01'), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'ALIG002'), 5, 4.25, 21.25),
((SELECT boleta_venta_id FROM `BoletasVenta` WHERE `numero_boleta` = 'BV003-2025-06-02'), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD002'), 1, 180.00, 180.00),
((SELECT boleta_venta_id FROM `BoletasVenta` WHERE `numero_boleta` = 'BV003-2025-06-02'), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'ALIG002'), 10, 4.25, 42.50),
((SELECT boleta_venta_id FROM `BoletasVenta` WHERE `numero_boleta` = 'BV004-2025-06-03'), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'JUGT001'), 3, 15.00, 45.00);

-- -----------------------------------------------------
-- Insertar NotasBaja
-- -----------------------------------------------------
INSERT INTO `NotasBaja` (`lote_id`, `producto_id`, `motivo_baja`, `cantidad_baja`, `fecha_baja`, `usuario_id`, `observaciones`) VALUES
((SELECT lote_id FROM `LotesProducto` WHERE `codigo_lote` = 'LOTE-AMX001' AND `producto_id` = (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD001')), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD001'), 'Vencido', 5, '2025-06-05', (SELECT usuario_id FROM `Usuarios` WHERE `nombre_acceso` = 'jalmacen'), 'Lote de Amoxipet vencido detectado en inventario.'),
((SELECT lote_id FROM `LotesProducto` WHERE `codigo_lote` = 'LOTE-RCM001' AND `producto_id` = (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD002')), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'PROD002'), 'Deteriorado', 1, '2025-06-06', (SELECT usuario_id FROM `Usuarios` WHERE `nombre_acceso` = 'jalmacen'), 'Bolsa de Royal Canin dañada durante manipulación.'),
((SELECT lote_id FROM `LotesProducto` WHERE `codigo_lote` = 'LOTE-SHP001' AND `producto_id` = (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'HIG001')), (SELECT producto_id FROM `Productos` WHERE `codigo_producto` = 'HIG001'), 'Muestra', 2, '2025-06-08', (SELECT usuario_id FROM `Usuarios` WHERE `nombre_acceso` = 'jalmacen'), 'Dos unidades de shampoo usadas como muestra para promoción.');
