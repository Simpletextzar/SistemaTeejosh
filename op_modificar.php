<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú de Modificaciones</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .menu-container {
            max-width: 700px;
            margin: 80px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            text-align: center;
            padding: 30px;
        }
        h1 {
            margin-bottom: 30px;
            color: #333;
        }
        .option {
            display: block;
            background-color: #333;
            color: white;
            text-decoration: none;
            padding: 15px 0;
            border-radius: 8px;
            margin: 10px 0;
            transition: background-color 0.3s;
            font-size: 18px;
        }
        .option:hover {
            background-color: #555;
        }
        .navbar {
            background-color: #222;
            color: white;
            padding: 10px 20px;
            text-align: left;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <?php
    include 'navbar.php'; // si ya tienes una barra de navegación
    ?>

    <div class="menu-container">
        <h1>Menú de Modificaciones</h1>
        <a href="modificar_producto.php" class="option">Modificar un Producto</a>
        <a href="restock_item.php" class="option">Restock de un Producto</a>
        <a href="reabastecimiento.php" class="option">Reabastecer un Producto</a>
    </div>

</body>
</html>
