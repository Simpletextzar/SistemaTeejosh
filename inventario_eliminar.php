<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Producto </title>
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
        <h2>Eliminar Producto del Inventario</h2>
        <?php
        if (isset($_POST['eliminar'])) {
            $id = $_POST['id'];
            $delete = pg_query($connection, "DELETE FROM item WHERE id = '$id'");
            if ($delete) {
                echo "<p>Producto  eliminado correctamente.</p>";
            } else {
                echo "<p>Error al eliminar el Producto.</p>";
            }
        }

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
        ?>

        <form method="post" id="formEliminar">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Edición</th>
                    <th>Lenguaje</th>
                    <th>Fecha de ingreso</th>
                    <th>Acción</th>
                </tr>
                <?php
                while ($row = pg_fetch_assoc($result)) {
                    echo "
                    <tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nombre']}</td>
                        <td>{$row['precio']}</td>
                        <td>{$row['cantidad']}</td>
                        <td>{$row['edicion']}</td>
                        <td>{$row['lenguaje']}</td>
                        <td>{$row['fecha_ingreso']}</td>
                        <td>
                            <button type='button' onclick=\"confirmarEliminacion('{$row['id']}')\">Eliminar</button>
                        </td>
                    </tr>
                    ";
                }
                ?>
            </table>
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="eliminar" value="1">
        </form>
    </div>

    <script>
    function confirmarEliminacion(id) {
        if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
            document.getElementById('id').value = id;
            document.getElementById('formEliminar').submit();
        }
    }
    </script>
</body>
</html>