<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    h1, h2 { color: #333; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    th { background-color: #f0f0f0; }
  </style>

</head>


<body>
  <h1>Resumen de Inventario - PAWPRO</h1>

  <p><strong>Total de productos:</strong> <?= $totalProductos['total'] ?></p>
  <p><strong>Total de marcas:</strong> <?= $totalMarcas['total'] ?></p>

  <h2>Producto con más stock</h2>
  <p><strong>Nombre:</strong> <?= $productoMasStock['nombre'] ?></p>
  <p><strong>Stock:</strong> <?= $productoMasStock['stock'] ?></p>

  <h2>Top 3 productos con menor stock</h2>
  <table>
    <tr><th>Producto</th><th>Stock</th></tr>
    <?php foreach ($top3MenorStock as $p): ?>
      <tr><td><?= $p['nombre'] ?></td><td><?= $p['stock'] ?></td></tr>
    <?php endforeach; ?>
  </table>

  <h2>Productos próximos a vencer</h2>
  <table>
    <tr><th>Producto</th><th>Fecha de vencimiento</th></tr>
    <?php foreach ($productosPorVencer as $p): ?>
      <tr><td><?= $p['nombre'] ?></td><td><?= $p['fecha_vencimiento'] ?></td></tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
