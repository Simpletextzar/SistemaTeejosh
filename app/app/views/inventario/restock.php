<?php
/**
 * ============================================================================
 * SISTEMA DE RESTOCK AUTOMÁTICO - GESTIÓN DE INVENTARIO EN CADENA
 * ============================================================================
 * 
 * ARCHIVO: restock.php
 * UBICACIÓN: /app/views/inventario/restock.php
 * 
 * DESCRIPCIÓN:
 * Vista especializada para el proceso de restock automático entre productos relacionados.
 * Permite transferir stock de un producto "fuente" (ej: caja cerrada) a un producto "destino" 
 * (ej: unidades individuales) siguiendo relaciones predefinidas de conversión.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Sistema de restock entre productos relacionados (fuente → destino)
 * - Autocompletado inteligente con stock actual visible
 * - Cálculo automático basado en unidades por item
 * - Validación de cantidades y relaciones de conversión
 * - Feedback visual con mensajes de éxito/error
 * 
 * DATOS IMPORTANTES:
 * - 🟡 COMPLEJIDAD: Media
 * - 🔄 PROCESO: Transferencia stock entre productos relacionados
 * - 🎯 RESPONSABILIDAD: Gestión de conversiones de inventario
 * - 💡 CASO USO: Ej. Caja de 36 sobres → 36 sobres individuales
 * - 🎨 DISEÑO: Formulario vertical con estilos modernos
 * 
 * RELACIONES:
 * - USADO POR: InventarioController::restock()
 * - UTILIZA: layouts/header.php, layouts/navbar.php, layouts/footer.php
 * - INTERACTÚA CON:
 *   - $items (lista de productos disponibles para restock)
 *   - $message (feedback de operaciones)
 *   - $title (título de la página)
 * 
 * EJEMPLOS DE USO:
 * ```php
 * // Ejemplo práctico de restock:
 * // Item fuente: "Caja de sobres Magic" (ID: 101, Stock: 10 cajas)
 * // Item destino: "Sobre Magic individual" (ID: 102, Stock: 50 unidades)
 * // Unidades por item: 36 (cada caja contiene 36 sobres)
 * // Cantidad a abrir: 2 cajas
 * // Resultado: 
 * //   - Fuente: 10 - 2 = 8 cajas
 * //   - Destino: 50 + (2 * 36) = 122 unidades
 * ```
 * 
 * FLUJO DE RESTOCK:
 * 1. Usuario selecciona producto fuente (stock a disminuir)
 * 2. Usuario selecciona producto destino (stock a aumentar)
 * 3. Define relación de conversión (unidades por item)
 * 4. Especifica cantidad de items fuente a convertir
 * 5. Sistema valida stock disponible y realiza transferencia
 * 6. Muestra confirmación o error
 * 
 * NOTAS CSS/HTML:
 * - DISEÑO: Contenedor centrado con sombra y bordes redondeados
 * - FORMULARIO: Layout vertical con flexbox
 * - COLORES:
 *   - Botón: Verde (#22941c) con hover más oscuro
 *   - Éxito: Verde bootstrap (#155724) con fondo #d4edda
 *   - Error: Rojo bootstrap (#721c24) con fondo #f8d7da
 * - ELEMENTOS ESPECIALES:
 *   - datalist con información de stock en labels
 *   - inputs numéricos con validación min="1"
 *   - transiciones CSS en hover
 * - RESPONSIVE: Ancho máximo 800px, se adapta a móviles
 */

require_once VIEW_PATH . '/layouts/header.php';
require_once VIEW_PATH . '/layouts/navbar.php';
?>

<style>
    /* ESTILOS ESPECÍFICOS PARA FORMULARIO DE RESTOCK */
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5; /* Fondo gris claro */
        margin: 0;
    }
    
    .container {
        max-width: 800px; /* Ancho máximo para legibilidad */
        margin: 50px auto; /* Centrado vertical y horizontal */
        background: white; /* Fondo blanco para el formulario */
        padding: 30px;
        border-radius: 10px; /* Bordes redondeados */
        box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Sombra suave */
    }
    
    h1 { 
        text-align: center; 
        color: #333; /* Color oscuro para buen contraste */
    }
    
    /* Formulario en columna vertical */
    form { 
        display: flex; 
        flex-direction: column; 
        gap: 1rem; /* Espaciado consistente entre elementos */
    }
    
    label { 
        font-weight: bold; /* Labels en negrita para mejor jerarquía */
    }
    
    input, button {
        padding: 10px;
        border-radius: 8px; /* Bordes redondeados consistentes */
        border: 1px solid #ccc; /* Borde sutil */
    }
    
    /* Botón principal de acción */
    button {
        background-color: rgb(34, 148, 28); /* Verde positivo para acción principal */
        color: white;
        cursor: pointer;
        border: none;
        font-weight: 500;
        transition: background-color 0.3s; /* Transición suave en hover */
    }
    
    button:hover { 
        background-color: rgb(25, 108, 20); /* Verde más oscuro al hover */
    }
    
    /* Mensajes de éxito con estilo bootstrap-like */
    .success { 
        color: #155724;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        padding: 15px;
        border-radius: 5px;
        margin: 20px 0;
        text-align: center;
    }
    
    /* Mensajes de error con estilo bootstrap-like */
    .error { 
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        padding: 15px;
        border-radius: 5px;
        margin: 20px 0;
        text-align: center;
    }
</style>

<!-- CONTENEDOR PRINCIPAL DEL FORMULARIO DE RESTOCK -->
<div class="container">
    <h1><?= $title ?></h1>
    
    <!-- SISTEMA DE MENSAJES DE FEEDBACK -->
    <?php if (!empty($message)): ?>
        <p class="<?= $message['success'] ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message['message']) ?>
        </p>
    <?php endif; ?>
    
    <!-- FORMULARIO DE RESTOCK -->
    <form method="POST" action="<?= url('inventario/restock') ?>">
        <!-- ITEM FUENTE: Producto del cual se extrae stock -->
        <label>Item fuente (Ejemplo: Caja de sobres):</label>
        <input list="items_fuente" name="item_fuente" placeholder="Escribe o selecciona..." required>
        <datalist id="items_fuente">
            <?php foreach ($items as $item): ?>
                <option value="<?= htmlspecialchars($item['id']) ?>" 
                        label="<?= htmlspecialchars($item['producto'] . ' (Stock: ' . $item['cantidad'] . ')') ?>">
            <?php endforeach; ?>
        </datalist>

        <!-- ITEM DESTINO: Producto al cual se agrega stock -->
        <label>Item destino (Ejemplo: Sobres individuales):</label>
        <input list="items_destino" name="item_destino" placeholder="Escribe o selecciona..." required>
        <datalist id="items_destino">
            <?php foreach ($items as $item): ?>
                <option value="<?= htmlspecialchars($item['id']) ?>" 
                        label="<?= htmlspecialchars($item['producto'] . ' (Stock: ' . $item['cantidad'] . ')') ?>">
            <?php endforeach; ?>
        </datalist>

        <!-- RELACIÓN DE CONVERSIÓN: Unidades por item fuente -->
        <label>Unidades por item (Ej: 36 sobres por caja):</label>
        <input type="number" name="unidades_por_item" min="1" required>

        <!-- CANTIDAD: Items fuente a procesar -->
        <label>Cantidad de items a abrir (Ej: 1 caja):</label>
        <input type="number" name="cantidad" min="1" required>

        <!-- BOTÓN DE EJECUCIÓN -->
        <button type="submit" name="restock" value="1">Procesar Restock</button>
    </form>
</div>

<?php require_once VIEW_PATH . '/layouts/footer.php'; ?>