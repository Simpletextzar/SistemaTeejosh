<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Venta</title>

    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/styles.css">

    <style>
        .venta-container {
            max-width: 950px;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 0.5rem;
        }
        button {
            padding: 0.6rem;
            cursor: pointer;
            background: #333;
            color: white;
            border: none;
            border-radius: 4px;
            margin-top: 10px;
        }
        button:hover {
            background: #555;
        }
    </style>
</head>
<body>

<?php
include '../includes/db_connect.php';
include '../includes/navbar.php';
date_default_timezone_set('America/Lima');
?>

<div class="venta-container">

    <h1>Registrar Venta</h1>

    <form method="POST" action="registrar_venta.php">

        <!-- MÉTODOS DE PAGO Y DATOS DE VENTA -->
        <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:1rem;">

            <div>
                <label>Fecha:</label>
                <input type="date" name="fecha" 
                       value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div>
                <label>Hora:</label>
                <input type="time" name="hora" 
                       value="<?php echo date('H:i'); ?>" required>
            </div>

            <div>
                <label>Método de pago:</label>
                <select name="m_pago" required>
                    <option>Efectivo</option>
                    <option>Tarjeta</option>
                    <option>Yape</option>
                    <option>Plin</option>
                    <option>Depósito bancario</option>
                </select>
            </div>

        </div>


        <h2 style="margin-top:25px;">Productos vendidos</h2>

        <!-- TABLA DINÁMICA DE PRODUCTOS -->
        <table id="tabla-productos">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio unitario (S/)</th>
                    <th>Total (S/)</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

            <tr>
                <!-- PRODUCTO -->
                <td>
                    <select name="id_producto[]" class="prod-select" required>
                        <option value="">Seleccione</option>

                        <?php
                        $sql = "
                            SELECT 
                                item.id_producto,
                                producto.nombre,
                                item.precio,
                                item.cantidad
                            FROM item
                            INNER JOIN producto 
                                ON producto.id = item.id_producto
                            ORDER BY producto.nombre;
                        ";
                        $result = pg_query($connection, $sql);

                        while ($row = pg_fetch_assoc($result)) {
                            echo '<option value="' . $row['id_producto'] . '" data-precio="'.$row['precio'].'">'
                                . htmlspecialchars($row['nombre'])
                                . ' (Stock: ' . $row['cantidad'] . ')'
                                . '</option>';
                        }
                        ?>
                    </select>
                </td>

                <!-- CANTIDAD -->
                <td>
                    <input type="number" name="cantidad[]" 
                           min="1" value="1" 
                           class="cant-input" required>
                </td>

                <!-- PRECIO -->
                <td>
                    <input type="number" name="precio_unit[]" 
                           step="0.01" min="0.01" 
                           class="precio-input" required>
                </td>

                <!-- TOTAL -->
                <td>
                    <input type="number" class="total-input" 
                           step="0.01" min="0" readonly>
                </td>

                <td>
                    <button type="button" class="delete-row">X</button>
                </td>
            </tr>

            </tbody>
        </table>

        <button type="button" id="add-row">Agregar otro producto</button>


        <h3 style="margin-top:20px;">Monto total de la venta</h3>
        <input type="number" step="0.01" name="monto_total" 
               id="monto-total" readonly required>

        <button type="submit" name="registrar" style="margin-top:25px;">
            Registrar Venta
        </button>

    </form>

</div>


<script>
// ===============================
//  AGREGAR NUEVA FILA
// ===============================
document.getElementById("add-row").addEventListener("click", function () {
    const tbody = document.querySelector("#tabla-productos tbody");
    const fila = tbody.rows[0].cloneNode(true);

    // Limpiar valores
    fila.querySelectorAll("input").forEach(i => i.value = "");
    fila.querySelector(".cant-input").value = 1;
    fila.querySelector(".prod-select").value = "";

    tbody.appendChild(fila);
});

// ===============================
//  ELIMINAR FILA
// ===============================
document.addEventListener("click", function(e) {
    if (e.target.classList.contains("delete-row")) {
        const filas = document.querySelectorAll("#tabla-productos tbody tr");
        if (filas.length > 1) e.target.closest("tr").remove();
        recalcularTotales();
    }
});

// ===============================
//  CÁLCULO AUTOMÁTICO DE TOTALES
// ===============================
function recalcularTotales() {
    let totalVenta = 0;

    document.querySelectorAll("#tabla-productos tbody tr").forEach(fila => {

        const cant = parseFloat(fila.querySelector(".cant-input").value) || 0;
        const precio = parseFloat(fila.querySelector(".precio-input").value) || 0;

        const total = cant * precio;
        fila.querySelector(".total-input").value = total.toFixed(2);

        totalVenta += total;
    });

    document.getElementById("monto-total").value = totalVenta.toFixed(2);
}

// Cambios que recalculan
document.addEventListener("input", function(e) {
    if (e.target.classList.contains("cant-input") ||
        e.target.classList.contains("precio-input")) {
        recalcularTotales();
    }
});

// Al seleccionar producto, autollenar precio
document.addEventListener("change", function(e) {
    if (e.target.classList.contains("prod-select")) {
        const precio = e.target.selectedOptions[0].dataset.precio;
        if (precio) e.target.closest("tr").querySelector(".precio-input").value = precio;
        recalcularTotales();
    }
});
</script>

</body>
</html>
