<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú Principal - Inventario</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php 
    session_start();
    if (!isset($_SESSION['loggedin'])) {
        header('Location: login.php');
        exit;
    }

    include 'navbar.php'; 
    ?>

    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

    <h1>Menú Principal - Módulo de Inventario</h1>

    <div class="menu">
        <a href="inventario_insertar.php">Insertar a inventario</a>
        <a href="op_modificar.php">Modificar inventario</a>
        <a href="inventario_eliminar.php">Eliminar inventario</a>
        <a href="items.php">Mostrar inventario</a>
    </div>
</body>
</html>
