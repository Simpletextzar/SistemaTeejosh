<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <?php include 'db_connect.php'; ?>
    <h2>Productos del inventario</h2>
    <?php
    $result = pg_query($connection, "SELECT * FROM producto");

    ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
        </tr>

        <?php
        while($row = pg_fetch_assoc($result)) {
            echo "
            <tr>
                <td>$row[id]</td>
                <td>$row[nombre]</td>
                <td>$row[descripcion]</td>
            </tr>
            ";
        }

        ?>
    </table>
</body>
</html>