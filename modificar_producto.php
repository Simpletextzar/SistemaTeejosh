<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Producto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        label {
            font-weight: bold;
        }
        input, textarea, button {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        textarea { resize: none; }
        button {
            grid-column: 1 / -1;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover { background-color: #555; }
        .disabled {
            opacity: 0.6;
            pointer-events: none;
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<?php
include 'navbar.php';
include 'db_connect.php';
?>

<div class="container">
    <h1>Modificar Producto</h1>

    <?php
    // Cargar listas desde la base de datos
    $productos_query = pg_query($connection, "SELECT id, nombre FROM producto ORDER BY nombre;");
    $categorias_query = pg_query($connection, "SELECT id, nombre FROM categoria ORDER BY nombre;");
    $franquicias_query = pg_query($connection, "SELECT id, nombre FROM franquicia ORDER BY nombre;");

    $producto = null;

    // Si el usuario eligió un producto por nombre
    if (isset($_POST['producto_nombre']) && $_POST['producto_nombre'] !== '') {
        $nombre = pg_escape_string($connection, $_POST['producto_nombre']);
        $producto_query = pg_query($connection, "SELECT * FROM producto WHERE nombre = '$nombre' LIMIT 1;");
        $producto = pg_fetch_assoc($producto_query);
    }
    ?>

    <!-- SELECCIONAR PRODUCTO -->
    <form method="POST">
        <label for="producto_nombre">Selecciona o escribe el nombre del producto:</label>
        <input list="lista_productos" name="producto_nombre" placeholder="Escribe o selecciona..." onchange="this.form.submit()" value="<?php echo htmlspecialchars($_POST['producto_nombre'] ?? ''); ?>">
        <datalist id="lista_productos">
            <?php
            while ($row = pg_fetch_assoc($productos_query)) {
                echo "<option value='" . htmlspecialchars($row['nombre']) . "'>";
            }
            ?>
        </datalist>
    </form>

    <!-- FORMULARIO DE MODIFICACIÓN -->
    <form method="POST" <?php echo !$producto ? 'class="disabled"' : ''; ?>>
        <input type="hidden" name="producto_id" value="<?php echo $producto['id'] ?? ''; ?>">
        <input type="hidden" name="producto_nombre" value="<?php echo htmlspecialchars($_POST['producto_nombre'] ?? ''); ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre'] ?? ''); ?>" required <?php echo !$producto ? 'disabled' : ''; ?>>

        <label>Descripción:</label>
        <textarea name="descripcion" rows="2" required <?php echo !$producto ? 'disabled' : ''; ?>><?php echo htmlspecialchars($producto['descripcion'] ?? ''); ?></textarea>

        <label>Categoría:</label>
        <input list="lista_categorias" name="id_categoria" placeholder="Escribe o selecciona..." value="<?php echo $producto['id_categoria'] ?? ''; ?>" <?php echo !$producto ? 'disabled' : ''; ?>>
        <datalist id="lista_categorias">
            <?php
            while ($cat = pg_fetch_assoc($categorias_query)) {
                echo "<option value='" . htmlspecialchars($cat['id']) . "' label='" . htmlspecialchars($cat['nombre']) . "'>";
            }
            ?>
        </datalist>

        <label>Franquicia:</label>
        <input list="lista_franquicias" name="id_franquicia" placeholder="Escribe o selecciona..." value="<?php echo $producto['id_franquicia'] ?? ''; ?>" <?php echo !$producto ? 'disabled' : ''; ?>>
        <datalist id="lista_franquicias">
            <?php
            while ($fr = pg_fetch_assoc($franquicias_query)) {
                echo "<option value='" . htmlspecialchars($fr['id']) . "' label='" . htmlspecialchars($fr['nombre']) . "'>";
            }
            ?>
        </datalist>

        <button type="submit" name="modificar" <?php echo !$producto ? 'disabled' : ''; ?>>Guardar Cambios</button>
    </form>

    <?php
    // Procesar actualización
    if (isset($_POST['modificar'])) {
        $id = (int)$_POST['producto_id'];
        $nombre = pg_escape_string($connection, $_POST['nombre']);
        $descripcion = pg_escape_string($connection, $_POST['descripcion']);
        $id_categoria = (int)$_POST['id_categoria'];
        $id_franquicia = (int)$_POST['id_franquicia'];

        $sql_func = "SELECT fn_modificar_producto($id, '$nombre', '$descripcion', $id_categoria, $id_franquicia) AS mensaje;";
        $result_func = pg_query($connection, $sql_func);
        $row = pg_fetch_assoc($result_func);

        echo "<p class='success'>" . htmlspecialchars($row['mensaje']) . "</p>";
    }
    ?>
</div>

</body>
</html>
