<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restock de Productos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h1 { text-align: center; color: #333; }
        form { display: flex; flex-direction: column; gap: 1rem; }
        input, button {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #333;
            color: white;
            cursor: pointer;
        }
        button:hover { background-color: #555; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<?php
include '../includes/db_connect.php';
include '../includes/navbar.php';

// Cargar todos los ítems disponibles
$items_query = pg_query($connection, "
    SELECT i.id, p.nombre AS producto, i.cantidad
    FROM item i
    JOIN producto p ON p.id = i.id_producto
    ORDER BY p.nombre;
");
?>

<div class="container">
    <h1>Restock de Productos</h1>
    <form method="POST">
        <label>Item fuente (Ejemplo: Caja de sobres):</label>
        <input list="items_fuente" name="item_fuente" placeholder="Escribe o selecciona..." required>
        <datalist id="items_fuente">
            <?php
            while ($row = pg_fetch_assoc($items_query)) {
                echo "<option value='" . htmlspecialchars($row['id']) . "' label='" . htmlspecialchars($row['producto'] . ' (Stock: ' . $row['cantidad'] . ')') . "'>";
            }
            ?>
        </datalist>

        <?php
        // Reiniciar cursor para reutilizar los mismos ítems
        pg_result_seek($items_query, 0);
        ?>

        <label>Item destino (Ejemplo: Sobres individuales):</label>
        <input list="items_destino" name="item_destino" placeholder="Escribe o selecciona..." required>
        <datalist id="items_destino">
            <?php
            while ($row = pg_fetch_assoc($items_query)) {
                echo "<option value='" . htmlspecialchars($row['id']) . "' label='" . htmlspecialchars($row['producto'] . ' (Stock: ' . $row['cantidad'] . ')') . "'>";
            }
            ?>
        </datalist>

        <label>Unidades por item (Ej: 36 sobres por caja):</label>
        <input type="number" name="unidades_por_item" min="1" required>

        <label>Cantidad de items a abrir (Ej: 1 caja):</label>
        <input type="number" name="cantidad" min="1" required>

        <button type="submit" name="restock">Procesar Restock</button>
    </form>

    <?php
    if (isset($_POST['restock'])) {
        $item_fuente = (int)$_POST['item_fuente'];
        $item_destino = (int)$_POST['item_destino'];
        $unidades_por_item = (int)$_POST['unidades_por_item'];
        $cantidad = (int)$_POST['cantidad'];

        $sql = "SELECT fn_restock_item($item_fuente, $item_destino, $unidades_por_item, $cantidad) AS mensaje;";
        $res = pg_query($connection, $sql);
        $row = pg_fetch_assoc($res);

        echo "<p class='success'>" . htmlspecialchars($row['mensaje']) . "</p>";
    }
    ?>
</div>

</body>
</html>
