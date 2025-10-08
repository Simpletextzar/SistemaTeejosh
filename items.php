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
    <?php
    session_start();
    include 'navbar.php';
    include 'db_connect.php'; 
    ?>

    <div class="full-container">
        <h2>Items del inventario</h2>
        
        <?php include 'formulario_busqueda.php'; ?>
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
            //Comprobamos que tengamos informacion de sesion de items, caso contrario cargamos todo
            if (isset($_SESSION['items'])) {
                $items = $_SESSION['items'];
            } else {
                $sql = "
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
                ";
                $result = pg_query($connection, $sql);
                $items = [];
                while ($row = pg_fetch_assoc($result)) {
                    $items[] = $row;
                }
            }

            //Impresion de las filas
            foreach ($items as $row) {
                echo "
                <tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nombre']}</td>
                    <td>{$row['precio']}</td>
                    <td>{$row['cantidad']}</td>
                    <td>{$row['edicion']}</td>
                    <td>{$row['lenguaje']}</td>
                    <td>{$row['fecha_ingreso']}</td>
                </tr>
                ";
            }

            unset($_SESSION['items']);

            ?>

        </table>
    </div>
</body>
</html>