CREATE TABLE categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

INSERT INTO categorias (nombre) VALUES
('Lácteos'), ('Granos'), ('Aceites'), ('Limpieza'), ('Higiene'),
('Snacks'), ('Bebidas'), ('Conservas'), ('Salsas');