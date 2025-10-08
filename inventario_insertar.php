<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .insert-form {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            max-width: 1000px;
            margin: 0 auto;
        }
        .insert-form h2 {
            grid-column: 1 / -1;
        }
        .insert-form label, 
        .insert-form input, 
        .insert-form textarea, 
        .insert-form button {
            width: 100%;
        }
        /* Segunda fila: que ocupe todas las columnas */
        .second-row {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        .insert-form button {
            margin-top: 1rem;
            grid-column: 1 / -1;
            padding: 0.6rem;
            background: #333;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .insert-form button:hover {
            background: #555;
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <?php
    session_start();
    include 'navbar.php';
    include 'db_connect.php';
    ?>

    <div class="full-container">
        <?php
        $sql_producto = "SELECT MAX(id) AS max_id FROM producto;";
        $result_producto = pg_query($connection, $sql_producto);
        $row_producto = pg_fetch_assoc($result_producto);
        $nuevo_id_producto = ($row_producto['max_id'] ?? 0) + 1;
        ?>

        <h1>Añadir Item</h1>

        <form method="POST" class="insert-form">
            <h2>Agregar nuevo producto, ID: <?php echo $nuevo_id_producto; ?></h2>

            <!-- Primer nivel -->
            <div>
                <label for="nombre">Nombre del producto:</label>
                <input type="text" name="nombre" required>
            </div>

            <div>
                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" rows="1" required></textarea>
            </div>

            <div>
                <label for="categoria">Categoría:</label>
                <input list="categorias" name="categoria" required>
                <datalist id="categorias">
                    <?php
                    $sql_categoria = "SELECT nombre FROM categoria ORDER BY nombre ASC;";
                    $result_categoria = pg_query($connection, $sql_categoria);
                    while ($row = pg_fetch_assoc($result_categoria)) {
                        echo '<option value="' . htmlspecialchars($row['nombre']) . '"></option>';
                    }
                    ?>
                </datalist>
            </div>

            <div>
                <label for="precio">Precio:</label>
                <input type="number" step="0.01" min="0.01" name="precio" required>
            </div>

            <!-- Segundo nivel -->
            <div class="second-row">
                <div>
                    <label for="franquicia">Franquicia:</label>
                    <input list="franquicias" name="franquicia" required>
                    <datalist id="franquicias">
                        <?php
                        $sql_franquicia = "SELECT nombre FROM franquicia ORDER BY nombre ASC;";
                        $result_franquicia = pg_query($connection, $sql_franquicia);
                        while ($row = pg_fetch_assoc($result_franquicia)) {
                            echo '<option value="' . htmlspecialchars($row['nombre']) . '"></option>';
                        }
                        ?>
                    </datalist>
                </div>

                <div>
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" min="1" name="cantidad" required>
                </div>

                <div>
                    <label for="edicion">Edición (opcional):</label>
                    <input list="ediciones" name="edicion">
                    <datalist id="ediciones">
                        <?php
                        $sql_edicion = "SELECT nombre FROM edicion ORDER BY nombre ASC;";
                        $result_edicion = pg_query($connection, $sql_edicion);
                        while ($row = pg_fetch_assoc($result_edicion)) {
                            echo '<option value="' . htmlspecialchars($row['nombre']) . '"></option>';
                        }
                        ?>
                    </datalist>
                </div>

                <div>
                    <label for="lenguaje">Lenguaje:</label>
                    <input list="lenguajes" name="lenguaje" required>
                    <datalist id="lenguajes">
                        <?php
                        $sql_lenguajes = "SELECT nombre FROM lenguaje ORDER BY nombre ASC;";
                        $result_lenguajes = pg_query($connection, $sql_lenguajes);
                        while ($row = pg_fetch_assoc($result_lenguajes)) {
                            echo '<option value="' . htmlspecialchars($row['nombre']) . '"></option>';
                        }
                        ?>
                    </datalist>
                </div>
            </div>

            <button type="submit" name="insertar">Insertar item</button>
        </form>

        <?php
        if (isset($_POST['insertar'])) {
            $id_producto = $nuevo_id_producto;
            $nombre = pg_escape_string($connection, $_POST['nombre']);
            $descripcion = pg_escape_string($connection, $_POST['descripcion']);
            $categoria_nombre = pg_escape_string($connection, $_POST['categoria']);
            $franquicia_nombre = pg_escape_string($connection, $_POST['franquicia']);
            $edicion_nombre = pg_escape_string($connection, $_POST['edicion'] ?? '');
            $lenguaje_nombre = pg_escape_string($connection, $_POST['lenguaje']);
            $precio = (float)$_POST['precio'];
            $cantidad = (int)$_POST['cantidad'];

            // Función que crea un registro si no existe
            $get_or_create_id = function($tabla, $nombre) use ($connection) {
                if (empty($nombre)) return 'NULL';
                $query = "SELECT id FROM $tabla WHERE nombre = '$nombre' LIMIT 1;";
                $res = pg_query($connection, $query);
                $row = pg_fetch_assoc($res);
                if ($row) {
                    return (int)$row['id'];
                } else {
                    // Obtener nuevo id incremental
                    $sql_max = "SELECT MAX(id) AS max_id FROM $tabla;";
                    $res_max = pg_query($connection, $sql_max);
                    $row_max = pg_fetch_assoc($res_max);
                    $nuevo_id = ($row_max['max_id'] ?? 0) + 1;

                    $insert_sql = "INSERT INTO $tabla (id, nombre) VALUES ($nuevo_id, '$nombre');";
                    $ok_insert = pg_query($connection, $insert_sql);

                    if ($ok_insert) {
                        return $nuevo_id;
                    } else {
                        echo "<p class='error'>Error al insertar nuevo valor en '$tabla': " . pg_last_error($connection) . "</p>";
                        return 'NULL';
                    }
                }
            };

            // Obtener o crear los IDs
            $id_categoria = $get_or_create_id('categoria', $categoria_nombre);
            $id_franquicia = $get_or_create_id('franquicia', $franquicia_nombre);
            $id_edicion = $get_or_create_id('edicion', $edicion_nombre);
            $id_lenguaje = $get_or_create_id('lenguaje', $lenguaje_nombre);

            if ($id_categoria === 'NULL' || $id_franquicia === 'NULL' || $id_lenguaje === 'NULL') {
                echo "<p class='error'>Error: no se pudieron obtener o crear algunos valores relacionados.</p>";
            } else {
                $sql_producto_insert = "
                    INSERT INTO producto (id, nombre, descripcion, id_categoria, id_franquicia)
                    VALUES ($id_producto, '$nombre', '$descripcion', $id_categoria, $id_franquicia);
                ";

                $ok_producto = pg_query($connection, $sql_producto_insert);

                if ($ok_producto) {
                    $sql_item_insert = "
                        INSERT INTO item (id_producto, precio, cantidad, id_edicion, id_lenguaje, fecha_ingreso)
                        VALUES ($id_producto, $precio, $cantidad, $id_edicion, $id_lenguaje, NOW());
                    ";

                    $ok_item = pg_query($connection, $sql_item_insert);

                    if ($ok_item) {
                        echo "<p class='success'>Producto e item agregados correctamente.</p>";
                    } else {
                        echo "<p class='error'>Error al insertar el item: " . pg_last_error($connection) . "</p>";
                    }
                } else {
                    echo "<p class='error'>Error al insertar el producto: " . pg_last_error($connection) . "</p>";
                }
            }
        }
        ?>
    </div>
</body>
</html>
