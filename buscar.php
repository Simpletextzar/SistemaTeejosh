<?php
session_start();
include 'db_connect.php';
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
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
$params = [];
if ($query !== '') {
    if (is_numeric($query)) {
        $sql .= " WHERE item.id = $1";
        $params[] = $query;
    } else {
        $sql .= " WHERE LOWER(producto.nombre) LIKE LOWER($1)";
        $params[] = '%' . $query . '%';
    }
}

if (!empty($params)) {
    $result = pg_query_params($connection, $sql, $params);
} else {
    $result = pg_query($connection, $sql);
}

$items = [];
while ($row = pg_fetch_assoc($result)) {
    $items[] = $row;
}
$_SESSION['items'] = $items;
header("Location: items.php");
exit;