USE registro_productos;

INSERT INTO productos (
  id_producto, nombre, descripcion, precio_compra, precio_venta,
  stock, categoria, subcategoria, marca, presentacion,
  fecha_vencimiento, fabricante, peso_volumen, unidad_medida,
  ubicacion_almacen, notas
) VALUES
('P00001', 'Leche Gloria Entera 1L', 'Leche UHT entera en tetrapack', 3.50, 4.50, 150, 'Lácteos', 'Leche', 'Gloria', 'Caja 1L', '2025-03-01', 'Gloria S.A.', 1.00, 'L', 'Estante A1', ''),
('P00002', 'Arroz Costeño 5kg', 'Arroz extra grano largo', 12.00, 16.00, 200, 'Granos', 'Arroz', 'Costeño', 'Saco 5kg', NULL, 'Costeño SAC', 5.00, 'kg', 'Estante B2', ''),
('P00003', 'Aceite Primor 1L', 'Aceite vegetal de soya', 5.80, 7.20, 100, 'Aceites', 'Vegetal', 'Primor', 'Botella', '2026-01-15', 'Alicorp', 1.00, 'L', 'Estante A2', ''),
('P00004', 'Detergente Ariel 800g', 'Polvo para lavar ropa', 6.50, 8.00, 75, 'Limpieza', 'Detergente', 'Ariel', 'Bolsa', '2027-07-10', 'P&G', 0.80, 'kg', 'Estante C3', ''),
('P00005', 'Papel Higiénico Elite x12', 'Rollo doble hoja', 9.90, 13.50, 60, 'Higiene', 'Papel', 'Elite', 'Pack x12', NULL, 'CMPC', 1.20, 'kg', 'Estante D1', ''),
('P00006', 'Galletas Oreo 154g', 'Galletas de chocolate con crema', 2.80, 3.90, 45, 'Snacks', 'Galletas', 'Oreo', 'Paquete', '2025-12-31', 'Mondelez', 0.15, 'kg', 'Estante B1', ''),
('P00007', 'Agua San Luis 625ml', 'Agua mineral sin gas', 1.20, 1.80, 300, 'Bebidas', 'Agua', 'San Luis', 'Botella', '2026-06-01', 'Coca Cola', 0.625, 'L', 'Estante A3', ''),
('P00008', 'Atún Florida 170g', 'Atún en aceite vegetal', 4.20, 5.50, 90, 'Conservas', 'Pescado', 'Florida', 'Lata', '2027-03-15', 'Florida S.A.', 0.17, 'kg', 'Estante E1', ''),
('P00009', 'Mayonesa Alacena 250g', 'Mayonesa clásica con huevo', 3.40, 4.80, 85, 'Salsas', 'Mayonesa', 'Alacena', 'Frasco', '2025-10-10', 'Alicorp', 0.25, 'kg', 'Estante F1', ''),
('P00010', 'Yogurt Laive Fresa 1L', 'Yogurt saborizado bajo en grasa', 4.50, 6.00, 50, 'Lácteos', 'Yogurt', 'Laive', 'Botella 1L', '2025-08-20', 'Laive S.A.', 1.00, 'L', 'Estante A4', '');
