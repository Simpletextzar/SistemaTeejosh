<?php
/**
 * ============================================================================
 * FORMULARIO DE CREACIÓN DE PRODUCTOS - INTERFAZ DE INSERCIÓN
 * ============================================================================
 * 
 * ARCHIVO: create.php
 * UBICACIÓN: /app/views/inventario/create.php
 * 
 * DESCRIPCIÓN:
 * Vista para agregar nuevos productos al inventario del sistema Teejosh.
 * Implementa un formulario avanzado con diseño grid CSS de 4 columnas,
 * autocompletado inteligente para categorías, franquicias, ediciones y lenguajes,
 * y generación automática de ID único para nuevos productos.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Formulario de creación con diseño grid de 4 columnas
 * - Autocompletado con datalist para campos predefinidos
 * - Generación y visualización de ID único automático
 * - Validación HTML5 para campos requeridos y tipos de datos
 * - Mensajes de feedback para éxito/error en operaciones
 * - Diseño responsivo con múltiples niveles de grid
 * 
 * DATOS IMPORTANTES:
 * - 🟡 COMPLEJIDAD: Media-Alta
 * - 🔐 SEGURIDAD: Validación HTML5, protección XSS en datos dinámicos
 * - 🎯 RESPONSABILIDAD: Inserción segura de nuevos productos
 * - 🎨 DISEÑO: Grid CSS de 4 columnas con estilos inline
 * - 📝 DATOS: Generación automática de $nuevo_id
 * 
 * RELACIONES:
 * - USADO POR: InventarioController::create()
 * - UTILIZA: layouts/header.php, layouts/navbar.php, layouts/footer.php
 * - INTERACTÚA CON:
 *   - $nuevo_id (ID automático generado)
 *   - $categorias, $franquicias, $ediciones, $lenguajes (listas desplegables)
 *   - $message (feedback de operaciones)
 *   - $title (título de la página)
 * 
 * EJEMPLOS DE USO:
 * ```php
 * // En InventarioController:
 * public function create() {
 *     $inventario = $this->model('Inventario');
 *     $data = [
 *         'title' => 'Agregar Nuevo Producto',
 *         'nuevo_id' => $inventario->generarNuevoID(),
 *         'categorias' => $inventario->getCategorias(),
 *         'franquicias' => $inventario->getFranquicias(),
 *         'ediciones' => $inventario->getEdiciones(),
 *         'lenguajes' => $inventario->getLenguajes(),
 *         'message' => $mensaje_operacion // éxito/error
 *     ];
 *     $this->view('inventario/create', $data);
 * }
 * ```
 * 
 * FLUJO DE CREACIÓN:
 * 1. Usuario accede a "Insertar a inventario" desde el dashboard
 * 2. Sistema genera automáticamente un nuevo ID único
 * 3. Usuario completa formulario con autocompletado asistido
 * 4. Validación HTML5 verifica campos requeridos y formatos
 * 5. Datos se envían al controlador para inserción en BD
 * 6. Sistema muestra mensaje de confirmación o error
 * 
 * NOTAS CSS/HTML:
 * - DISEÑO: Grid de 4 columnas con contenedor de 1000px máximo
 * - ESTRUCTURA: Dos niveles - primera fila (4 campos) + segunda fila (4 campos en sub-grid)
 * - ELEMENTOS ESPECIALES:
 *   - datalist para autocompletado en categorías, franquicias, ediciones, lenguajes
 *   - textarea de una línea para descripción
 *   - input type="number" con step y min para precios y cantidades
 * - COLORES:
 *   - Botón: #333 (hover: #555)
 *   - Éxito: green
 *   - Error: red
 * - RESPONSIVE: Grid se adapta, elementos ocupan 100% de ancho en su celda
 * - ACCESIBILIDAD: Labels asociados, campos required, placeholders claros
 */

require_once VIEW_PATH . '/layouts/header.php';
require_once VIEW_PATH . '/layouts/navbar.php';
?>

<style>
    /* ESTILOS ESPECÍFICOS PARA FORMULARIO DE CREACIÓN */
    .insert-form {
        display: grid;
        grid-template-columns: repeat(4, 1fr); /* 4 columnas iguales */
        gap: 1rem; /* Espaciado consistente entre elementos */
        max-width: 1000px; /* Ancho máximo para pantallas grandes */
        margin: 0 auto; /* Centrado horizontal */
    }
    
    /* Título que ocupa las 4 columnas */
    .insert-form h2 {
        grid-column: 1 / -1; /* Expandir through completo del grid */
    }
    
    /* Elementos de formulario al 100% de su contenedor */
    .insert-form label, 
    .insert-form input, 
    .insert-form textarea, 
    .insert-form button {
        width: 100%;
    }
    
    /* Segunda fila como sub-grid de 4 columnas */
    .second-row {
        grid-column: 1 / -1; /* Ocupar las 4 columnas principales */
        display: grid;
        grid-template-columns: repeat(4, 1fr); /* Sub-grid de 4 columnas */
        gap: 1rem; /* Espaciado consistente */
    }
    
    /* Botón principal de envío */
    .insert-form button {
        margin-top: 1rem;
        grid-column: 1 / -1; /* Ocupar ancho completo */
        padding: 0.6rem;
        background: #333; /* Negro/gris oscuro */
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 4px; /* Bordes ligeramente redondeados */
    }
    
    /* Efecto hover para botón */
    .insert-form button:hover {
        background: #555; /* Gris más claro al hover */
    }
    
    /* Mensajes de feedback */
    .success { 
        color: green; 
        text-align: center; 
    }
    
    .error { 
        color: red; 
        text-align: center; 
    }
</style>

<!-- CONTENEDOR PRINCIPAL DEL FORMULARIO -->
<div class="full-container">
    <h1><?= $title ?></h1>

    <!-- SISTEMA DE MENSAJES DE FEEDBACK -->
    <?php if (!empty($message)): ?>
        <p class="<?= $message['success'] ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message['message']) ?>
        </p>
    <?php endif; ?>

    <!-- FORMULARIO DE CREACIÓN DE PRODUCTO -->
    <form method="POST" action="<?= BASE_URL ?>inventario/create" class="insert-form">
        <h2>Agregar nuevo producto, ID: <?= $nuevo_id ?></h2>

        <!-- PRIMERA FILA: 4 CAMPOS PRINCIPALES -->
        
        <!-- Campo: Nombre del producto -->
        <div>
            <label for="nombre">Nombre del producto:</label>
            <input type="text" name="nombre" required>
        </div>

        <!-- Campo: Descripción -->
        <div>
            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" rows="1" required></textarea>
        </div>

        <!-- Campo: Categoría con autocompletado -->
        <div>
            <label for="categoria">Categoría:</label>
            <input list="categorias" name="categoria" required>
            <datalist id="categorias">
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['nombre']) ?>">
                <?php endforeach; ?>
            </datalist>
        </div>

        <!-- Campo: Precio con validación numérica -->
        <div>
            <label for="precio">Precio:</label>
            <input type="number" step="0.01" min="0.01" name="precio" required>
        </div>

        <!-- SEGUNDA FILA: SUB-GRID DE 4 CAMPOS -->
        <div class="second-row">
            
            <!-- Campo: Franquicia con autocompletado -->
            <div>
                <label for="franquicia">Franquicia:</label>
                <input list="franquicias" name="franquicia" required>
                <datalist id="franquicias">
                    <?php foreach ($franquicias as $fr): ?>
                        <option value="<?= htmlspecialchars($fr['nombre']) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>

            <!-- Campo: Cantidad con validación mínima -->
            <div>
                <label for="cantidad">Cantidad:</label>
                <input type="number" min="1" name="cantidad" required>
            </div>

            <!-- Campo: Edición opcional con autocompletado -->
            <div>
                <label for="edicion">Edición (opcional):</label>
                <input list="ediciones" name="edicion">
                <datalist id="ediciones">
                    <?php foreach ($ediciones as $ed): ?>
                        <option value="<?= htmlspecialchars($ed['nombre']) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>

            <!-- Campo: Lenguaje con autocompletado -->
            <div>
                <label for="lenguaje">Lenguaje:</label>
                <input list="lenguajes" name="lenguaje" required>
                <datalist id="lenguajes">
                    <?php foreach ($lenguajes as $len): ?>
                        <option value="<?= htmlspecialchars($len['nombre']) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <!-- BOTÓN DE ENVÍO PRINCIPAL -->
        <button type="submit" name="insertar" value="1">Insertar item</button>
    </form>
</div>

<?php require_once VIEW_PATH . '/layouts/footer.php'; ?>