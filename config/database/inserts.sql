-- -----------------------------------------------------
-- Insertar las categorias
-- -----------------------------------------------------

USE pawpro_database;
INSERT INTO Categoria (nombre_categoria, descripcion) VALUES ('Medicamentos','Medicamentos utilizado para los animales');
INSERT INTO Categoria (nombre_categoria, descripcion) VALUES ('Suplementos', 'Suplementos utilizados para animales');
INSERT INTO Categoria (nombre_categoria, descripcion) VALUES ('Antipulgas', 'Las mejores antipulgas para tu mascota');
INSERT INTO Categoria (nombre_categoria, descripcion) VALUES ('Alimentos', 'Alimentos balanceados para la salud de tu mascota');


-- -----------------------------------------------------
-- Insertar Marcas
-- (Asumimos que estos serán marca_id: 1, 2, 3)
-- -----------------------------------------------------
INSERT INTO Marcas (nombre_marca, codigo_marca) VALUES ('Bayer', 'BAY01');
INSERT INTO Marcas (nombre_marca, codigo_marca) VALUES ('Royal Canin', 'ROY01');
INSERT INTO Marcas (nombre_marca, codigo_marca) VALUES ('Zoetis', 'ZOE01');
INSERT INTO Marcas (nombre_marca, codigo_marca) VALUES ('MSD Salud Animal', 'MSD01');

-- -----------------------------------------------------
-- Insertar Subcategorias
-- (Asumimos que las Categoria ids 1:Medicamentos, 2:Suplementos, 3:Antipulgas, 4:Alimentos ya existen)
-- (Asumimos que estos serán id_subcategoria: 1, 2, 3, 4, 5)
-- -----------------------------------------------------
-- Subcategorías para Medicamentos (id_categoria = 1)
INSERT INTO Subcategoria (id_categoria, nombre_subcategoria, descripcion) VALUES (1, 'Antibióticos', 'Medicamentos para tratar infecciones bacterianas');
INSERT INTO Subcategoria (id_categoria, nombre_subcategoria, descripcion) VALUES (1, 'Analgésicos', 'Medicamentos para aliviar el dolor');
-- Subcategorías para Alimentos (id_categoria = 4)
INSERT INTO Subcategoria (id_categoria, nombre_subcategoria, descripcion) VALUES (4, 'Alimento Seco para Perros', 'Croquetas y pienso seco para perros');
INSERT INTO Subcategoria (id_categoria, nombre_subcategoria, descripcion) VALUES (4, 'Alimento Húmedo para Gatos', 'Latas y pouches de alimento húmedo para gatos');
-- Subcategorías para Antipulgas (id_categoria = 3)
INSERT INTO Subcategoria (id_categoria, nombre_subcategoria, descripcion) VALUES (3, 'Pipetas', 'Soluciones tópicas para el control de pulgas y garrapatas');


-- -----------------------------------------------------
-- Insertar Presentaciones
-- (Asumimos que estos serán id_presentacion: 1, 2, 3, 4)
-- -----------------------------------------------------
INSERT INTO Presentacion (nombre_presentacion) VALUES ('Caja con 10 Tabletas');
INSERT INTO Presentacion (nombre_presentacion) VALUES ('Frasco Gotero 10ml');
INSERT INTO Presentacion (nombre_presentacion) VALUES ('Bolsa 3kg');
INSERT INTO Presentacion (nombre_presentacion) VALUES ('Pipeta individual');


-- -----------------------------------------------------
-- Insertar Productos
-- (Usaremos los IDs asumidos de Marcas, Subcategorias, Presentaciones)
-- -----------------------------------------------------

-- Producto 1: Antibiótico
INSERT INTO Productos (
    codigo_producto, nombre_producto, descripcion, marca_id, precio_venta_unitario,
    stock_minimo, stock_maximo, unidad_medida, estado, subcategoria_id, presentacion_id
) VALUES (
    'PROD001', 'Amoxipet 250mg', 'Amoxicilina para perros y gatos. Tratamiento de infecciones.',
    1,          -- Bayer (marca_id=1)
    25.50,      -- precio_venta_unitario
    10,         -- stock_minimo
    50,         -- stock_maximo
    'unidad',   -- unidad_medida
    'activo',   -- estado
    1,          -- Antibióticos (id_subcategoria=1)
    1           -- Caja con 10 Tabletas (id_presentacion=1)
);

-- Producto 2: Alimento para perro
INSERT INTO Productos (
    codigo_producto, nombre_producto, descripcion, marca_id, precio_venta_unitario,
    stock_minimo, stock_maximo, unidad_medida, estado, subcategoria_id, presentacion_id
) VALUES (
    'PROD002', 'Royal Canin Maxi Adulto', 'Alimento balanceado para perros adultos de raza grande.',
    2,          -- Royal Canin (marca_id=2)
    180.00,
    5,
    30,
    'bolsa',
    'activo',
    3,          -- Alimento Seco para Perros (id_subcategoria=3)
    3           -- Bolsa 3kg (id_presentacion=3)
);

-- Producto 3: Antipulgas en Pipeta
INSERT INTO Productos (
    codigo_producto, nombre_producto, descripcion, marca_id, precio_venta_unitario,
    stock_minimo, stock_maximo, unidad_medida, estado, subcategoria_id, presentacion_id
) VALUES (
    'PROD003', 'Frontline Plus Perros M', 'Pipeta antipulgas y garrapatas para perros medianos (10-20kg).',
    4,          -- MSD Salud Animal (marca_id=4)
    45.00,
    20,
    100,
    'unidad',
    'activo',
    5,          -- Pipetas (id_subcategoria=5)
    4           -- Pipeta individual (id_presentacion=4)
);


-- -----------------------------------------------------
-- Insertar Lotes de Productos (Stock Inicial)
-- -----------------------------------------------------

-- Lote para Producto 1 (Amoxipet 250mg, producto_id=1)
INSERT INTO LotesProducto (
    producto_id, codigo_lote, cantidad_actual, fecha_vencimiento, precio_compra_unitario, fecha_ingreso
) VALUES (
    1, 'LOTE-AMX001', 30, '2026-12-31', 15.00, '2025-05-19'
);
INSERT INTO LotesProducto (
    producto_id, codigo_lote, cantidad_actual, fecha_vencimiento, precio_compra_unitario, fecha_ingreso
) VALUES (
    1, 'LOTE-AMX002', 20, '2027-03-31', 15.50, '2025-05-19'
);


-- Lote para Producto 2 (Royal Canin Maxi Adulto, producto_id=2)
INSERT INTO LotesProducto (
    producto_id, codigo_lote, cantidad_actual, fecha_vencimiento, precio_compra_unitario, fecha_ingreso
) VALUES (
    2, 'LOTE-RCM001', 20, '2026-10-01', 120.00, '2025-05-19'
);

-- Lote para Producto 3 (Frontline Plus Perros M, producto_id=3)
INSERT INTO LotesProducto (
    producto_id, codigo_lote, cantidad_actual, fecha_vencimiento, precio_compra_unitario, fecha_ingreso
) VALUES (
    3, 'LOTE-FRP001', 50, '2027-01-15', 28.00, '2025-05-19'
);
