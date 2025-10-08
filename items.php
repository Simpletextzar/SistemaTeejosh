<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="full-container">
        <h2>Items del inventario</h2>
        <?php
        $connection = pg_connect("host=aws-1-sa-east-1.pooler.supabase.com dbname=db_teejosh user=postgres.piearfkkossvytunnrfk password=patoloco090");
        if(!$connection) {
            echo "Error de conexion.<br>";
            exit;
        }

        $result = pg_query($connection, 
        "
        SELECT 
            item.id,
            producto.nombre AS nombre,
            item.precio,
            item.cantidad,
            COALESCE(edicion.nombre, '-') AS edicion,
            lenguaje.nombre AS lenguaje,
            to_char(item.fecha_ingreso, 'YYYY-MM-DD HH24:MI:SS') AS fecha_ingreso
        FROM item
        INNER JOIN producto ON item.id_producto = producto.id
        LEFT JOIN edicion ON item.id_edicion = edicion.id
        INNER JOIN lenguaje ON item.id_lenguaje = lenguaje.id
        ");

        ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Edición</th>
                <th>Lenguaje</th>
                <th>Fecha de ingreso</th>
            </tr>

            <?php
            while($row = pg_fetch_assoc($result)) {
                echo "
                <tr>
                    <td>$row[id]</td>
                    <td>$row[nombre]</td>
                    <td>$row[precio]</td>
                    <td>$row[cantidad]</td>
                    <td>$row[edicion]</td>
                    <td>$row[lenguaje]</td>
                    <td>$row[fecha_ingreso]</td>
                </tr>
                ";
            }

            ?>
        </table>
    </div>
</body>
</html>