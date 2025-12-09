<?php
/**
 * ============================================================================
 * FORMULARIO DE REABASTECIMIENTO SIMPLE - INCREMENTO DE STOCK
 * ============================================================================
 * 
 * ARCHIVO: reabastecer.php
 * UBICACIÓN: /app/views/inventario/reabastecer.php
 * 
 * DESCRIPCIÓN:
 * Vista para el reabastecimiento simple de productos existentes en el inventario.
 * Permite incrementar el stock de un producto específico mediante una interfaz
 * minimalista y directa, mostrando el stock actual para referencia.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Incremento simple de stock para productos existentes
 * - Autocompletado con información de stock actual
 * - Validación de cantidades positivas
 * - Feedback inmediato de operaciones
 * - Interfaz minimalista y enfocada
 * 
 * DATOS IMPORTANTES:
 * - 🟢 COMPLEJIDAD: Baja
 * - 📈 OPERACIÓN: Incremento simple de cantidades
 * - 🎯 RESPONSABILIDAD: Reabastecimiento directo de stock
 * - 💡 CASO USO: Reposición de inventario, compras nuevas
 * - 🎨 DISEÑO: Formulario compacto y minimalista
 * 
 * RELACIONES:
 * - USADO POR: InventarioController::reabastecer()
 * - UTILIZA: layouts/header.php, layouts/navbar.php, layouts/footer.php
 * - INTERACTÚA CON:
 *   - $items (lista de productos con stock actual)
 *   - $message (feedback de operaciones)
 *   - $title (título de la página)
 * 
 * EJEMPLOS DE USO:
 * ```php
 * // Ejemplo práctico de reabastecimiento:
 * // Producto: "Sobre Magic individual" (ID: 102, Stock actual: 50)
 * // Cantidad a agregar: 100 unidades
 * // Resultado: Stock nuevo = 50 + 100 = 150 unidades
 * ```
 * 
 * FLUJO DE REABASTECIMIENTO:
 * 1. Usuario selecciona producto a reabastecer
 * 2. Sistema muestra stock actual en el datalist
 * 3. Usuario ingresa cantidad a agregar
 * 4. Sistema valida que la cantidad sea positiva
 * 5. Se actualiza el stock en la base de datos
 * 6. Se muestra confirmación de la operación
 * 
 * NOTAS CSS/HTML:
 * - DISEÑO: Contenedor compacto con sombra sutil
 * - FORMULARIO: Layout vertical minimalista
 * - COLORES:
 *   - Botón: #333 (negro/gris oscuro) → #555 (hover)
 *   - Éxito: Verde simple
 *   - Error: Rojo simple
 * - ELEMENTOS ESPECIALES:
 *   - datalist con stock actual visible
 *   - input numérico con validación min="1"
 *   - labels en negrita para jerarquía clara
 * - RESPONSIVE: Ancho máximo 700px, elementos al 100%
 * - USABILIDAD: Enfoque en simplicidad y velocidad
 */

require_once VIEW_PATH . '/layouts/header.php';
require_once VIEW_PATH . '/layouts/navbar.php';
?>

<style>
    /* ESTILOS ESPECÍFICOS PARA FORMULARIO DE REABASTECIMIENTO */
    body { 
        font-family: Arial; 
        background: #f9f9f9; /* Fondo gris muy claro */
    }
    
    .container {
        max-width: 700px; /* Ancho máximo compacto */
        margin: 50px auto; /* Centrado con margen superior */
        background: white; /* Fondo blanco para el formulario */
        padding: 30px; 
        border-radius: 10px; /* Bordes redondeados */
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); /* Sombra sutil */
    }
    
    /* Elementos de formulario al 100% de ancho */
    input, button { 
        padding: 10px; 
        border: 1px solid #ccc; /* Borde sutil */
        border-radius: 8px; /* Bordes redondeados */
        width: 100%; /* Ancho completo del contenedor */
        margin-bottom: 15px; /* Separación entre elementos */
    }
    
    label { 
        font-weight: bold; /* Labels en negrita */
        display: block; /* Comportamiento de bloque */
        margin-bottom: 5px; /* Pequeño espacio debajo del label */
    }
    
    /* Botón de acción principal */
    button { 
        background: #333; /* Negro/gris oscuro */
        color: white; 
        cursor: pointer; 
    }
    
    button:hover { 
        background: #555; /* Gris más claro al hover */
    }
    
    /* Mensajes de feedback simples */
    .success { 
        color: green; /* Verde para éxito */
    }
    
    .error { 
        color: red; /* Rojo para error */
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
    
    <!-- FORMULARIO DE REABASTECIMIENTO -->
    <form method="POST" action="<?= BASE_URL ?>inventario/reabastecer">
        <!-- SELECCIÓN DE PRODUCTO CON STOCK ACTUAL VISIBLE -->
        <label for="item">Selecciona un item:</label>
        <input list="items" name="item" placeholder="Escribe o selecciona..." required>
        <datalist id="items">
            <?php foreach ($items as $item): ?>
                <option value="<?= htmlspecialchars($item['id']) ?>" 
                        label="<?= htmlspecialchars($item['producto'] . ' (Stock actual: ' . $item['cantidad'] . ')') ?>">
            <?php endforeach; ?>
        </datalist>

        <!-- CANTIDAD A AGREGAR -->
        <label for="cantidad">Cantidad a agregar:</label>
        <input type="number" name="cantidad" min="1" required>

        <!-- BOTÓN DE EJECUCIÓN -->
        <button type="submit" name="reabastecer" value="1">Reabastecer</button>
    </form>
</div>

<?php require_once VIEW_PATH . '/layouts/footer.php'; ?>