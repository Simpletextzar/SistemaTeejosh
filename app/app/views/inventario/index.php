<?php
/**
 * ============================================================================
 * VISTA DE LISTADO Y BÚSQUEDA DE INVENTARIO
 * ============================================================================
 * 
 * ARCHIVO: index.php
 * UBICACIÓN: /app/views/inventario/index.php
 * 
 * DESCRIPCIÓN:
 * Vista principal para visualizar el inventario completo del sistema Teejosh.
 * Proporciona funcionalidades de búsqueda por nombre o ID, y muestra todos los
 * productos en una tabla estructurada con todos sus atributos.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Visualización tabular de todo el inventario
 * - Sistema de búsqueda por nombre de producto o ID de ítem
 * - Botón "Mostrar todo" para resetear búsquedas
 * - Display completo de atributos de productos
 * - Integración con layout común del sistema
 * 
 * DATOS IMPORTANTES:
 * - 🟡 COMPLEJIDAD: Media
 * - 🔐 SEGURIDAD: Protección XSS en datos dinámicos
 * - 🎯 RESPONSABILIDAD: Visualización y búsqueda de inventario
 * - 📊 DISEÑO: Tabla responsiva con contenedor completo
 * 
 * RELACIONES:
 * - USADO POR: InventarioController::index() e InventarioController::buscar()
 * - UTILIZA: layouts/header.php, layouts/navbar.php, layouts/footer.php
 * - INTERACTÚA CON: Array $items (productos), $title (título página)
 * - ENLACES A: Sistema de búsqueda interno del controlador
 * 
 * EJEMPLOS DE USO:
 * ```php
 * // En InventarioController:
 * public function index() {
 *     $inventario = $this->model('Inventario');
 *     $data = [
 *         'title' => 'Inventario Completo',
 *         'items' => $inventario->getAll()
 *     ];
 *     $this->view('inventario/index', $data);
 * }
 * ```
 * 
 * FLUJO DE EJECUCIÓN:
 * 1. Usuario accede a "Mostrar inventario" desde el dashboard
 * 2. InventarioController carga todos los productos desde el modelo
 * 3. Se renderiza esta vista con los datos en formato tabla
 * 4. Usuario puede buscar productos específicos por nombre o ID
 * 5. Al buscar, se recarga la vista con resultados filtrados
 * 6. "Mostrar todo" restablece la vista completa
 * 
 * NOTAS CSS/HTML:
 * - CONTENEDOR: Clase "full-container" para ancho completo
 * - FORMULARIO: Contenedor con clase "form-container" para búsquedas
 * - TABLA: Estructura tabular tradicional con th y td
 * - BOTONES: Estilo básico, requieren estilos CSS externos
 * - ESTRUCTURA: Header → Navbar → Contenido principal → Tabla → Footer
 * - RESPONSIVE: Depende de los estilos externos en styles.css
 * - SEGURIDAD: htmlspecialchars() en datos dinámicos para prevenir XSS
 */

require_once VIEW_PATH . '/layouts/header.php';
require_once VIEW_PATH . '/layouts/navbar.php';
?>

<!-- CONTENEDOR PRINCIPAL DE LA VISTA DE INVENTARIO -->
<div class="full-container">
    <h2><?= $title ?></h2>
    
    <!-- SECCIÓN DE BÚSQUEDA Y FILTROS -->
    <div class="form-container">
        <h2>Buscar por nombre de producto o id ítem</h2>
        
        <!-- Formulario de búsqueda por texto -->
        <form action="<?= url('inventario/buscar') ?>" method="get">
            <input type="text" name="query" placeholder="Nombre o ID del ítem" required>
            <br>
            <button type="submit">Buscar</button>
        </form>

        <!-- Espaciado entre formularios -->
        
        <!-- Formulario para mostrar todo el inventario -->
        <form action="<?= url('inventario/index') ?>" method="get">
            <button type="submit">Mostrar todo</button>
        </form>
    </div>
    
    <!-- TABLA DE INVENTARIO -->
    <table>
        <!-- Encabezados de la tabla -->
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Edición</th>
            <th>Lenguaje</th>
            <th>Fecha de ingreso</th>
        </tr>

        <!-- Iteración sobre cada producto en el inventario -->
        <?php foreach ($items as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['nombre']) ?></td>
            <td><?= $row['precio'] ?></td>
            <td><?= $row['cantidad'] ?></td>
            <td><?= htmlspecialchars($row['edicion']) ?></td>
            <td><?= htmlspecialchars($row['lenguaje']) ?></td>
            <td><?= $row['fecha_ingreso'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once VIEW_PATH . '/layouts/footer.php'; ?>