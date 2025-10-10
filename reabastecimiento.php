<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reabastecer Stock</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; }
        .container {
            max-width: 700px; margin: 50px auto;
            background: white; padding: 30px; border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        input, button { padding: 10px; border: 1px solid #ccc; border-radius: 8px; width: 100%; }
        button { background: #333; color: white; cursor: pointer; }
        button:hover { background: #555; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<?php
include 'db_connect.php';
include 'navbar.php';

$items_query = pg_query($connection, "
    SELECT i.id, p.nombre AS producto, i.cantidad
    FROM item i
    JOIN producto p ON p.id = i.id_producto
    ORDER BY p.nombre;
");
?>

<div class="container">
    <h1>Reabastecer Stock</h1>
    <form method="POST">
        <label for="item">Selecciona un item:</label>
        <input list="items" name="item" placeholder="Escribe o selecciona..." required>
        <datalist id="items">
            <?php
            while ($row = pg_fetch_assoc($items_query)) {
                echo "<option value='" . htmlspecialchars($row['id']) . "' label='" . htmlspecialchars($row['producto'] . ' (Stock actual: ' . $row['cantidad'] . ')') . "'>";
            }
            ?>
        </datalist>

        <label for="cantidad">Cantidad a agregar:</label>
        <input type="number" name="cantidad" min="1" required>

        <button type="submit" name="reabastecer">Reabastecer</button>
    </form>

    <?php
    if (isset($_POST['reabastecer'])) {
        $item_id = (int)$_POST['item'];
        $cantidad = (int)$_POST['cantidad'];

        $sql = "SELECT fn_reabastecer_item($item_id, $cantidad) AS mensaje;";
        $res = pg_query($connection, $sql);
        $row = pg_fetch_assoc($res);

        echo "<p class='success'>" . htmlspecialchars($row['mensaje']) . "</p>";
    }
    ?>
</div>

</body>
</html>
