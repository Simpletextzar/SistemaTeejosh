<?php
include '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ==============================
    // VALIDAR DATOS DEL FORMULARIO
    // ==============================
    if (!isset($_POST['id_producto']) || !is_array($_POST['id_producto'])) {
        die("Error: No se enviaron productos.");
    }

    $fecha       = $_POST['fecha'];
    $hora        = $_POST['hora'];
    $m_pago      = $_POST['m_pago'];
    $monto_total = $_POST['monto_total'];

    // =============================================
    // ASERCIONES ANTES DEL PROCESAMIENTO DE DATOS
    // =============================================
    assert($monto_total > 0, "El total de la venta debe ser mayor que cero.");
    assert(count($_POST['id_producto']) > 0, "Debe existir al menos un producto en la venta.");

    // ==============================
    // REGISTRAR CABECERA DE VENTA
    // ==============================
    try {

        $sqlCab = "
            INSERT INTO reg_venta (fecha, hora, monto_total, m_pago)
            VALUES ($1, $2, $3, $4)
            RETURNING id_reg_venta
        ";

        $resCab = pg_query_params(
            $connection,
            $sqlCab,
            [$fecha, $hora, $monto_total, $m_pago]
        );

        if (!$resCab) {
            throw new Exception("Error al registrar la cabecera de la venta.");
        }

        $rowCab   = pg_fetch_assoc($resCab);
        $id_venta = $rowCab['id_reg_venta'];

    } catch (Exception $e) {

        echo "<script>
                alert('".$e->getMessage()."');
                window.history.back();
            </script>";
        exit;
    }

    // ==============================
    // REGISTRAR DETALLE DE PRODUCTOS
    // ==============================
    foreach ($_POST['id_producto'] as $i => $id_producto) {

        $cantidad = $_POST['cantidad'][$i];
        $precio   = $_POST['precio_unit'][$i];

        // ==================================================
        // ASERCIONES DE VERIFICACIÓN DE DATOS DEL PRODUCTO
        // ==================================================
    
        assert($precio > 0, "El precio debe ser mayor a cero.");

        // ==============================
        //  VALIDACIÓN 1: PRECIO
        // ==============================
        if ($precio <= 0) {
            echo "<script>
                    alert('Error: El precio del producto no es válido.');
                    window.history.back();
                </script>";
            exit;
        }

        // ==============================
        //  VALIDACIÓN 2: STOCK
        // ==============================
        $sqlStock = "SELECT cantidad FROM item WHERE id_producto = $1";
        $resStock = pg_query_params($connection, $sqlStock, [$id_producto]);
        $rowStock = pg_fetch_assoc($resStock);

        if ($cantidad > $rowStock['cantidad']) {
            echo "<script>
                    alert('Error: La cantidad solicitada ($cantidad) excede el stock disponible ({$rowStock['cantidad']}).');
                    window.history.back();
                </script>";
            exit;
        }

        // Calcular monto de la línea
        $monto = floatval($cantidad) * floatval($precio);

        // Insertar detalle de venta
        $sqlDet = "
            INSERT INTO public.producto_venta 
                (id_producto, id_reg_venta, cantidad, monto)
            VALUES ($1, $2, $3, $4)
        ";

        pg_query_params(
            $connection,
            $sqlDet,
            [$id_producto, $id_venta, $cantidad, $monto]
        );

        // ==============================
        //  DESCONTAR STOCK
        // ==============================

        $nuevoStock  = intval($rowStock['cantidad']) - intval($cantidad);

        $sqlUpdate = "
            UPDATE item 
            SET cantidad = $1 
            WHERE id_producto = $2
        ";

        pg_query_params($connection, $sqlUpdate, [$nuevoStock, $id_producto]);
    }

    echo "<script>
            alert('Venta registrada correctamente');
            window.location='venta.php';
        </script>";
}
?>
