USE `pawpro_database`;

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
('87654321', 'Luis Mendoza', 'Jr. Los Álamos 456', '912345678', 'luis.mendoza@example.com');

-- -----------------------------------------------------
-- Insertar Usuarios
-- -----------------------------------------------------
-- IMPORTANTE: En una aplicación real, 'contraseña_hash' debe almacenar una contraseña segura con hash, no texto plano.
INSERT INTO `Usuarios` (`nombre_usuario`, `nombre_acceso`, `contraseña_hash`, `correo_electronico`, `rol_id`, `estado`) VALUES
('Juan Pérez', 'jperez', 'hashed_password_123', 'juan.perez@example.com', (SELECT rol_id FROM `Roles` WHERE `nombre_rol` = 'Administrador'), 'activo'),
('María López', 'mlopez', 'hashed_password_456', 'maria.lopez@example.com', (SELECT rol_id FROM `Roles` WHERE `nombre_rol` = 'Encargado Almacen'), 'activo'),
('Carlos García', 'cgarcia', 'hashed_password_789', 'carlos.garcia@example.com', (SELECT rol_id FROM `Roles` WHERE `nombre_rol` = 'Encargado de ventas'), 'activo'),
('Admin Principal', 'admin', 'contraseña_segura_hash', 'admin@pawpro.vet', (SELECT rol_id FROM `Roles` WHERE `nombre_rol` = 'Administrador'), 'activo'),
('Juan Perez Almacen', 'jalmacen', 'otra_contraseña_hash', 'jalmacen@pawpro.vet', (SELECT rol_id FROM `Roles` WHERE `nombre_rol` = 'Encargado Almacen'), 'activo');

-- -----------------------------------------------------
-- Insertar Marcas
-- -----------------------------------------------------
INSERT INTO `Marcas` (`nombre_marca`, `codigo_marca`) VALUES
('Purina', 'PUR001'),
('Royal Canin', 'RCAN002'),
('Pedigree', 'PDG003'),
('Bayer', 'BAY01'),
('Zoetis', 'ZOE01'),
('MSD Salud Animal', 'MSD01');

-- -----------------------------------------------------
-- Insertar Categorias
-- -----------------------------------------------------
INSERT INTO `Categoria` (`nombre_categoria`, `descripcion`) VALUES
('Medicamentos','Medicamentos utilizado para los animales'),
('Suplementos', 'Suplementos utilizados para animales'),
('Antipulgas', 'Las mejores antipulgas para tu mascota'),
('Alimentos', 'Alimentos balanceados para la salud de tu mascota');

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
((SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Alimentos'), "Alimento Barf", NULL);

-- -----------------------------------------------------
-- Insertar Presentaciones
-- -----------------------------------------------------
INSERT INTO `Presentacion` (`nombre_presentacion`) VALUES
('Caja con 10 Tabletas'),
('Frasco Gotero 10ml'),
('Bolsa 3kg'),
('Pipeta individual');


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
    (SELECT id_presentacion FROM `Presentacion` WHERE `nombre_presentacion` = 'unidad' LIMIT 1), -- Assuming 'unidad' is a generic presentation or you might need to add it
    4.25,
    50,
    NULL,
    'activo',
    'unidad'
);

-- Producto 5: Juguete
INSERT INTO `Productos` (
    `codigo_producto`, `nombre_producto`, `descripcion`, `marca_id`, `subcategoria_id`, `presentacion_id`,
    `precio_venta_unitario`, `stock_minimo`, `stock_maximo`, `unidad_medida`, `estado`
) VALUES (
    'JUGT001', 'Juguete Pelota Duradera', 'Pelota de caucho resistente para perros grandes',
    (SELECT marca_id FROM `Marcas` WHERE `nombre_marca` = 'Pedigree'),
    (SELECT id_subcategoria FROM `Subcategoria` WHERE `nombre_subcategoria` = 'General' AND `id_categoria` = (SELECT id_categoria FROM `Categoria` WHERE `nombre_categoria` = 'Medicamentos')), -- Assuming 'General' category for toys for now, you might want a 'Accesorios' category
    (SELECT id_presentacion FROM `Presentacion` WHERE `nombre_presentacion` = 'unidad' LIMIT 1), -- Assuming 'unidad' is a generic presentation
    15.00,
    5,
    NULL,
    'activo',
    'unidad'
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

-- -----------------------------------------------------
-- Insertar Proveedores
-- -----------------------------------------------------
INSERT INTO `Proveedores` (`ruc`, `razon_social`, `ciudad`, `direccion`, `telefono_representante`, `correo_electronico`, `productos_distribuidos`, `estado`) VALUES
('20100062029', 'DISTRIBUIDORA VETERINARIA DEL NORTE S.A.C.', 'Trujillo', 'Av. España 1234, Centro Cívico', '044-200100', 'ventas@divetnorte.com.pe', 'Medicamentos veterinarios, alimentos balanceados, accesorios para mascotas.', 'activo'),
('20506070801', 'AGROVET MARKET ANIMAL HEALTH', 'Lima', 'Av. El Derby 254, Santiago de Surco', '01-6107900', 'contacto@agrovetmarket.com', 'Productos farmacéuticos y nutricionales para uso veterinario.', 'activo'),
('10458877221', 'IMPORTACIONES VET PERÚ E.I.R.L.', 'Arequipa', 'Calle Mercaderes 456, Cercado', '054-280510', 'info@vetperuimport.com', 'Equipamiento médico veterinario, insumos quirúrgicos.', 'activo'),
('20304050607', 'PET FOODS DEL PERÚ S.R.L.', 'Lima', 'Calle Los Alamos 321, Miraflores', '01-4478899', 'pedidos@petfoods.com.pe', 'Alimentos premium para perros y gatos, snacks.', 'inactivo');

-- -----------------------------------------------------
-- Insertar BoletasVenta (sin nuevos datos proporcionados, solo para estructura)
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Insertar DetallesBoletaVenta (sin nuevos datos proporcionados, solo para estructura)
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Insertar FacturasCompra (sin nuevos datos proporcionados, solo para estructura)
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Insertar DetallesFacturaCompra (sin nuevos datos proporcionados, solo para estructura)
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Insertar NotasBaja (sin nuevos datos proporcionados, solo para estructura)
-- -----------------------------------------------------