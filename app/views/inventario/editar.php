<?php
/**
 * ============================================================================
 * FORMULARIO DE EDICIÓN DE PRODUCTOS - INTERFAZ DE MODIFICACIÓN
 * ============================================================================
 * 
 * ARCHIVO: editar.php
 * UBICACIÓN: /app/views/inventario/editar.php
 * 
 * DESCRIPCIÓN:
 * Vista compleja para modificar productos existentes en el inventario.
 * Implementa un sistema de dos pasos: selección de producto + formulario de edición
 * con campos deshabilitados dinámicamente y autocompletado inteligente.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Sistema de dos formularios secuenciales
 * - Autocompletado con datalist para productos, categorías y franquicias
 * - Campos dinámicamente habilitados/deshabilitados según selección
 * - Validación en tiempo real con submit automático onchange
 * - Mensajes de feedback para éxito/error en operaciones
 * 
 * DATOS IMPORTANTES:
 * - 🚀 COMPLEJIDAD: Alta (múltiples estados y interacciones)
 * - 🔐 SEGURIDAD: Protección XSS, campos deshabilitados hasta selección
 * - 🎯 RESPONSABILIDAD: Modificación segura de datos de productos
 * - 🎨 DISEÑO: Grid CSS moderno con estilos inline
 * 
 * RELACIONES:
 * - USADO POR: InventarioController::editar()
 * - UTILIZA: layouts/header.php, layouts/navbar.php, layouts/footer.php
 * - INTERACTÚA CON: 
 *   - $producto (datos del producto seleccionado)
 *   - $productos (lista para autocompletar)
 *   - $categorias, $franquicias (listas desplegables)
 *   - $message (feedback de operaciones)
 * 
 * EJEMPLOS DE USO:
 * ```php
 * // En InventarioController:
 * public function editar() {
 *     $inventario = $this->model('Inventario');
 *     $data = [
 *         'title' => 'Editar Producto',
 *         'productos' => $inventario->getNombresProductos(),
 *         'categorias' => $inventario->getCategorias(),
 *         'franquicias' => $inventario->getFranquicias(),
 *         'producto' => $producto_seleccionado, // null inicialmente
 *         'message' => $mensaje_operacion // éxito/error
 *     ];
 *     $this->view('inventario/editar', $data);
 * }
 * ```
 * 
 * FLUJO DE INTERACCIÓN:
 * 1. Vista carga con formulario de selección activo y edición deshabilitado
 * 2. Usuario selecciona/escribe nombre de producto → submit automático
 * 3. Servidor busca producto y recarga vista con datos en formulario de edición
 * 4. Formulario de edición se habilita con datos pre-cargados
 * 5. Usuario modifica campos y envía cambios
 * 6. Sistema muestra mensaje de éxito/error
 * 
 * NOTAS CSS/HTML:
 * - DISEÑO: Grid de 2 columnas para formularios responsivos
 * - CONTENEDOR: Estilo card con sombra y bordes redondeados
 * - ESTADOS: Clase "disabled" para formulario no seleccionado
 * - COLORES: 
 *   - Éxito: green (success)
 *   - Error: red (error)  
 *   - Botones: #333 (hover: #555)
 * - ELEMENTOS ESPECIALES:
 *   - datalist para autocompletado nativo
 *   - textarea con resize: none
 *   - grid-column: 1 / -1 para botón ancho completo
 * - ACCESIBILIDAD: Campos required, placeholders descriptivos
 */

require_once VIEW_PATH . '/layouts/header.php';
require_once VIEW_PATH . '/layouts/navbar.php';
?>

<style>
    /* ESTILOS ESPECÍFICOS PARA FORMULARIO DE EDICIÓN */
    .container {
        max-width: 900px;
        margin: 50px auto;
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); /* Sombra suave elevada */
    }
    
    h1 {
        text-align: center;
        color: #333;
    }
    
    /* Diseño grid moderno para formularios */
    form {
        display: grid;
        grid-template-columns: 1fr 1fr; /* Dos columnas iguales */
        gap: 1rem; /* Espaciado consistente */
    }
    
    label {
        font-weight: bold;
    }
    
    input, textarea, button {
        width: 100%;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #ccc; /* Borde sutil */
    }
    
    textarea { 
        resize: none; /* Evitar redimensionamiento */
    }
    
    /* Botón principal que ocupa ambas columnas */
    button {
        grid-column: 1 / -1; /* Expandir through completo del grid */
        padding: 10px;
        background-color: #333; /* Negro/gris oscuro */
        color: white;
        border: none;
        cursor: pointer;
    }
    
    button:hover { 
        background-color: #555; /* Gris más claro al hover */
    }
    
    /* Estado deshabilitado para formulario no seleccionado */
    .disabled {
        opacity: 0.6;
        pointer-events: none; /* Deshabilitar interacciones */
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
<div class="container">
    <h1><?= $title ?></h1>

    <!-- SISTEMA DE MENSAJES DE FEEDBACK -->
    <?php if (!empty($message)): ?>
        <p class="<?= $message['success'] ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message['message']) ?>
        </p>
    <?php endif; ?>

    <!-- FORMULARIO 1: SELECCIÓN DE PRODUCTO -->
    <form method="POST" action="<?= BASE_URL ?>inventario/editar">
        <label for="producto_nombre">Selecciona o escribe el nombre del producto:</label>
        <input list="lista_productos" name="producto_nombre" 
               placeholder="Escribe o selecciona..." 
               onchange="this.form.submit()" 
               value="<?= htmlspecialchars($_POST['producto_nombre'] ?? '') ?>">
        
        <!-- Lista desplegable de productos disponibles -->
        <datalist id="lista_productos">
            <?php foreach ($productos as $prod): ?>
                <option value="<?= htmlspecialchars($prod['nombre']) ?>">
            <?php endforeach; ?>
        </datalist>
    </form>

    <!-- FORMULARIO 2: EDICIÓN DE PRODUCTO (condicional) -->
    <form method="POST" action="<?= BASE_URL ?>inventario/editar" 
          <?= !$producto ? 'class="disabled"' : '' ?>>
        
        <!-- Campos ocultos para mantener estado -->
        <input type="hidden" name="producto_id" value="<?= $producto['id'] ?? '' ?>">
        <input type="hidden" name="producto_nombre" 
               value="<?= htmlspecialchars($_POST['producto_nombre'] ?? '') ?>">

        <!-- Campo: Nombre del producto -->
        <label>Nombre:</label>
        <input type="text" name="nombre" 
               value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>" 
               required <?= !$producto ? 'disabled' : '' ?>>

        <!-- Campo: Descripción del producto -->
        <label>Descripción:</label>
        <textarea name="descripcion" rows="2" required 
                  <?= !$producto ? 'disabled' : '' ?>>
                  <?= htmlspecialchars($producto['descripcion'] ?? '') ?>
        </textarea>

        <!-- Campo: Categoría con autocompletado -->
        <label>Categoría:</label>
        <input list="lista_categorias" name="id_categoria" 
               placeholder="Escribe o selecciona..." 
               value="<?= $producto['id_categoria'] ?? '' ?>" 
               <?= !$producto ? 'disabled' : '' ?>>
        <datalist id="lista_categorias">
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= htmlspecialchars($cat['id']) ?>" 
                        label="<?= htmlspecialchars($cat['nombre']) ?>">
            <?php endforeach; ?>
        </datalist>

        <!-- Campo: Franquicia con autocompletado -->
        <label>Franquicia:</label>
        <input list="lista_franquicias" name="id_franquicia" 
               placeholder="Escribe o selecciona..." 
               value="<?= $producto['id_franquicia'] ?? '' ?>" 
               <?= !$producto ? 'disabled' : '' ?>>
        <datalist id="lista_franquicias">
            <?php foreach ($franquicias as $fr): ?>
                <option value="<?= htmlspecialchars($fr['id']) ?>" 
                        label="<?= htmlspecialchars($fr['nombre']) ?>">
            <?php endforeach; ?>
        </datalist>

        <!-- Botón de envío (solo habilitado si hay producto seleccionado) -->
        <button type="submit" name="modificar" value="1" 
                <?= !$producto ? 'disabled' : '' ?>>
                Guardar Cambios
        </button>
    </form>
</div>

<?php require_once VIEW_PATH . '/layouts/footer.php'; ?>