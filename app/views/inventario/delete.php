<?php
/**
 * ============================================================================
 * INTERFAZ DE ELIMINACIÓN DE PRODUCTOS - GESTIÓN DE BAJAS
 * ============================================================================
 * 
 * ARCHIVO: delete.php
 * UBICACIÓN: /app/views/inventario/delete.php
 * 
 * DESCRIPCIÓN:
 * Vista para eliminar productos del inventario del sistema Teejosh.
 * Presenta una tabla completa del inventario con botones de eliminación
 * individuales que incluyen confirmación JavaScript para prevenir eliminaciones
 * accidentales. Implementa un patrón de eliminación seguro con verificación.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Tabla completa de inventario con opción de eliminación por producto
 * - Confirmación JavaScript antes de cada eliminación
 * - Sistema de mensajes de feedback para operaciones
 * - Eliminación individual segura con ID específico
 * - Integración con layout común del sistema
 * 
 * DATOS IMPORTANTES:
 * - 🟡 COMPLEJIDAD: Media
 * - 🔐 SEGURIDAD: Confirmación JavaScript, protección XSS, eliminación por ID
 * - 🎯 RESPONSABILIDAD: Gestión segura de bajas de productos
 * - ⚠️  PELIGRO: Operación destructiva - requiere confirmación
 * - 💻 CLIENTE: Usa JavaScript para interacción del usuario
 * 
 * RELACIONES:
 * - USADO POR: InventarioController::delete()
 * - UTILIZA: layouts/header.php, layouts/navbar.php, layouts/footer.php
 * - INTERACTÚA CON:
 *   - $items (array de productos para mostrar)
 *   - $message (feedback de operaciones de eliminación)
 *   - $title (título de la página)
 * 
 * EJEMPLOS DE USO:
 * ```php
 * // En InventarioController:
 * public function delete() {
 *     $inventario = $this->model('Inventario');
 *     $data = [
 *         'title' => 'Eliminar Productos del Inventario',
 *         'items' => $inventario->getAll(),
 *         'message' => $mensaje_operacion // éxito/error en eliminación
 *     ];
 *     $this->view('inventario/delete', $data);
 * }
 * ```
 * 
 * FLUJO DE ELIMINACIÓN:
 * 1. Usuario accede a "Eliminar inventario" desde el dashboard
 * 2. Sistema carga todos los productos en tabla con botones de eliminación
 * 3. Usuario hace clic en "Eliminar" para un producto específico
 * 4. JavaScript muestra diálogo de confirmación
 * 5. Si usuario confirma, se envía formulario con ID del producto
 * 6. Controlador procesa eliminación y muestra resultado
 * 
 * NOTAS CSS/HTML:
 * - ESTRUCTURA: Tabla tradicional con columna adicional de acciones
 * - BOTONES: Estilo básico, requieren estilos CSS externos
 * - FORMULARIO: Único formulario que se envía con ID dinámico via JavaScript
 * - ELEMENTOS OCULTOS: Campos hidden para ID y acción de eliminación
 * - JAVASCRIPT: Función confirmarEliminacion() para diálogo de confirmación
 * - SEGURIDAD: 
 *   - htmlspecialchars() en datos dinámicos
 *   - Confirmación obligatoria antes de eliminar
 *   - Eliminación por ID específico
 * - ACCESIBILIDAD: Tabla semántica con th para encabezados
 */

require_once VIEW_PATH . '/layouts/header.php';
require_once VIEW_PATH . '/layouts/navbar.php';
?>

<!-- CONTENEDOR PRINCIPAL DE LA VISTA DE ELIMINACIÓN -->
<div class="full-container">
    <h2><?= $title ?></h2>
    
    <!-- SISTEMA DE MENSAJES DE FEEDBACK -->
    <?php if (!empty($message)): ?>
        <p class="<?= $message['success'] ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message['message']) ?>
        </p>
    <?php endif; ?>

    <!-- FORMULARIO PRINCIPAL DE ELIMINACIÓN -->
    <form method="post" action="<?= BASE_URL ?>inventario/delete" id="formEliminar">
        <!-- TABLA DE PRODUCTOS CON OPCIONES DE ELIMINACIÓN -->
        <table>
            <!-- ENCABEZADOS DE LA TABLA -->
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Edición</th>
                <th>Lenguaje</th>
                <th>Fecha de ingreso</th>
                <th>Acción</th> <!-- Columna adicional para botones de eliminación -->
            </tr>
            
            <!-- ITERACIÓN SOBRE CADA PRODUCTO EN EL INVENTARIO -->
            <?php foreach ($items as $row): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= $row['precio'] ?></td>
                <td><?= $row['cantidad'] ?></td>
                <td><?= htmlspecialchars($row['edicion']) ?></td>
                <td><?= htmlspecialchars($row['lenguaje']) ?></td>
                <td><?= $row['fecha_ingreso'] ?></td>
                <td>
                    <!-- BOTÓN DE ELIMINACIÓN CON CONFIRMACACIÓN JAVASCRIPT -->
                    <button type='button' onclick="confirmarEliminacion('<?= $row['id'] ?>')">
                        Eliminar
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <!-- CAMPOS OCULTOS PARA EL ENVÍO DEL FORMULARIO -->
        <input type="hidden" name="id" id="id">
        <input type="hidden" name="eliminar" value="1">
    </form>
</div>

<!-- SCRIPT JAVASCRIPT PARA CONFIRMACIÓN DE ELIMINACIÓN -->
<script>
/**
 * Función para confirmar la eliminación de un producto
 * 
 * DESCRIPCIÓN:
 * Muestra un diálogo de confirmación nativo del navegador antes de eliminar.
 * Previene eliminaciones accidentales al requerir confirmación explícita.
 * 
 * PARÁMETROS:
 * @param {string} id - ID del producto a eliminar
 * 
 * FUNCIONAMIENTO:
 * 1. Muestra diálogo confirm() con mensaje estándar
 * 2. Si usuario confirma (OK), establece ID en campo hidden
 * 3. Envía formulario automáticamente
 * 4. Si usuario cancela, no hace nada
 * 
 * EJEMPLO:
 * confirmarEliminacion('25') → 
 *   Diálogo: "¿Estás seguro de que deseas eliminar este producto?"
 *   Si OK: Envía formulario con id=25
 */
function confirmarEliminacion(id) {
    if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
        document.getElementById('id').value = id;
        document.getElementById('formEliminar').submit();
    }
}
</script>

<?php require_once VIEW_PATH . '/layouts/footer.php'; ?>