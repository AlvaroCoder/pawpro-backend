-- -----------------------------------------------------
-- Insertar las categorias
-- -----------------------------------------------------

USE pawpro_database;

INSERT INTO Categoria (nombre_categoria, descripcion) VALUES ('Medicamentos','Medicamentos utilizado para los animales');
INSERT INTO Categoria (nombre_categoria, descripcion) VALUES ('Suplementos', 'Suplementos utilizados para animales');
INSERT INTO Categoria (nombre_categoria, descripcion) VALUES ('Antipulgas', 'Las mejores antipulgas para tu mascota');
INSERT INTO Categoria (nombre_categoria, descripcion) VALUES ('Alimentos', 'Alimentos balanceados para la salud de tu mascota');

-- -----------------------------------------------------
-- Insertar las subcategorias
-- -----------------------------------------------------

INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Anestesicos");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Antianemicos");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Antibioticos");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Antiinflamatorios");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Hormonales");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Cicatrizantes");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Vitaminas");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Antiparasitarios");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Antiemeticos");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Analgesicos");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Protector gastrico");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Tranquilizantes");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Articulaciones");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "General");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (1, "Regenerador osteoarticular");

INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (2, "Leche materna");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (2, "Vitaminas");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (2, "Tranquilizantes");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (2, "Renales");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (2, "Piel y pelo");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (2, "Laxantes");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (2, "Hepatoprotector");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (2, "Articulaciones");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (2, "Antiparasitarios");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (2, "Antianemicos");

INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (3, "Spray");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (3, "Pipetas");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (3, "Pastillas");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (3, "Collares");

INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (4, "Alimentos medicados");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (4, "Alimentos no medicados");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (4, "Sazonador");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (4, "Premios/Snack");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (4, "Alimento Humedo");
INSERT INTO Subcategoria(id_categoria, nombre_subcategoria) VALUES (4, "Alimento Barf");

-- -----------------------------------------------------
-- Insertar las presentaciones
-- -----------------------------------------------------



-- -----------------------------------------------------
-- Insertar las marcas
-- -----------------------------------------------------


--- ---------
--- Insertar los roles
--- -----
INSERT INTO Roles(nombre_rol, descripcion) VALUES ('Administrador', 'El rol general que tiene todos los privilegios');
INSERT INTO Roles(nombre_rol, descripcion) VALUES ('Encargado de ventas', 'El rol para los trabajadores de ventas');
