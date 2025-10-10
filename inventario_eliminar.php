<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Producto</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <?php include 'db_connect.php'; ?>

    <h2>Gestión de productos - Eliminar del inventario</h2>

    <?php
    if (isset($_POST['eliminar'])) {
        $id = $_POST['id'];

        // Verificar si el producto existe
        $check_query = pg_query_params($connection, "SELECT * FROM producto WHERE id = $1", array($id));
        if (pg_num_rows($check_query) > 0) {
            // Eliminar producto
            $delete_query = pg_query_params($connection, "DELETE FROM producto WHERE id = $1", array($id));

            if ($delete_query) {
                echo "<p style='color:green;'>Producto con ID $id eliminado correctamente.</p>";
            } else {
                echo "<p style='color:red;'>Error al eliminar el producto.</p>";
            }
        } else {
            echo "<p style='color:red;'>No se encontró un producto con el ID ingresado.</p>";
        }
    }

    // Mostrar tabla actualizada
    $result = pg_query($connection, "SELECT * FROM producto ORDER BY id");

    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Accion</th>
          </tr>";

    while ($row = pg_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['nombre']}</td>
                <td>{$row['descripcion']}</td>
                <td>
                    <form method='POST' action='' onsubmit='return confirm(\"¿Seguro que deseas eliminar este producto?\");'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <button type='submit' name='eliminar'>Eliminar</button>
                    </form>
                </td>
              </tr>";
    }

    echo "</table>";
    ?>
</body>
</html>