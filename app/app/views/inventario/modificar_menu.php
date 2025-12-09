<?php
/**
 * ============================================================================
 * MENÚ DE MODIFICACIONES - CENTRO DE OPERACIONES DE INVENTARIO
 * ============================================================================
 * 
 * ARCHIVO: modificar_menu.php
 * UBICACIÓN: /app/views/inventario/modificar_menu.php
 * 
 * DESCRIPCIÓN:
 * Vista de menú centralizado que sirve como hub para todas las operaciones de modificación
 * del inventario. Proporciona acceso organizado a las tres funciones principales de
 * actualización: edición, restock y reabastecimiento.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Menú de navegación centralizado para operaciones de modificación
 * - Acceso rápido a las tres funciones principales de actualización
 * - Diseño limpio y minimalista centrado en usabilidad
 * - Transiciones suaves para mejor experiencia de usuario
 * 
 * DATOS IMPORTANTES:
 * - 🟢 COMPLEJIDAD: Baja
 * - 🎯 RESPONSABILIDAD: Punto de entrada unificado para modificaciones
 * - 📱 DISEÑO: Menú centrado con opciones de tarjeta
 * - 🔗 NAVEGACIÓN: Redirección simple a funciones específicas
 * 
 * RELACIONES:
 * - USADO POR: InventarioController::modificar_menu()
 * - UTILIZA: layouts/header.php, layouts/navbar.php, layouts/footer.php
 * - INTERACTÚA CON: $title (título de la página)
 * - ENLACES A: 
 *   - inventario/editar (Modificar un Producto)
 *   - inventario/restock (Restock de un Producto) 
 *   - inventario/reabastecer (Reabastecer un Producto)
 * 
 * EJEMPLOS DE USO:
 * ```php
 * // En InventarioController:
 * public function modificar_menu() {
 *     $data = ['title' => 'Menú de Modificaciones'];
 *     $this->view('inventario/modificar_menu', $data);
 * }
 * ```
 * 
 * FLUJO DE NAVEGACIÓN:
 * 1. Usuario accede a "Modificar inventario" desde dashboard principal
 * 2. Sistema muestra este menú con tres opciones principales
 * 3. Usuario selecciona la operación deseada
 * 4. Redirección a la vista correspondiente
 * 5. Cada vista maneja su lógica específica de modificación
 * 
 * NOTAS CSS/HTML:
 * - DISEÑO: Contenedor centrado con sombra y bordes redondeados
 * - OPCIONES: Botones tipo tarjeta con hover effects
 * - COLORES:
 *   - Fondo: #f2f2f2 (gris muy claro)
 *   - Opciones: #333 (negro/gris oscuro) → #555 (hover)
 *   - Texto: Blanco para contraste
 * - TIPOGRAFÍA: Arial, sans-serif
 * - EFECTOS: Transiciones suaves de 0.3s en hover
 * - RESPONSIVE: Ancho máximo 700px, se adapta a móviles
 * - ACCESIBILIDAD: Enlaces semánticos <a> con estilos de botón
 */

require_once VIEW_PATH . '/layouts/header.php';
require_once VIEW_PATH . '/layouts/navbar.php';
?>

<style>
    /* ESTILOS ESPECÍFICOS PARA MENÚ DE MODIFICACIONES */
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2; /* Fondo gris muy claro */
        margin: 0;
        padding: 0;
    }
    
    /* Contenedor principal del menú */
    .menu-container {
        max-width: 700px; /* Ancho máximo para menú compacto */
        margin: 80px auto; /* Margen superior generoso y centrado */
        background: white; /* Fondo blanco para el menú */
        border-radius: 10px; /* Bordes redondeados */
        box-shadow: 0 0 10px rgba(0,0,0,0.2); /* Sombra más pronunciada */
        text-align: center; /* Centrado de contenido */
        padding: 30px; /* Espaciado interno */
    }
    
    h1 {
        margin-bottom: 30px; /* Espacio debajo del título */
        color: #333; /* Color oscuro para buen contraste */
    }
    
    /* Opciones del menú con estilo de botón/tarjeta */
    .option {
        display: block; /* Comportamiento de bloque para ocupar ancho completo */
        background-color: #333; /* Fondo oscuro */
        color: white; /* Texto blanco para contraste */
        text-decoration: none; /* Sin subrayado */
        padding: 15px 0; /* Espaciado vertical generoso */
        border-radius: 8px; /* Bordes redondeados */
        margin: 10px 0; /* Separación entre opciones */
        transition: background-color 0.3s; /* Transición suave en hover */
        font-size: 18px; /* Tamaño de fuente legible */
    }
    
    /* Efecto hover para feedback visual */
    .option:hover {
        background-color: #555; /* Fondo más claro al hover */
    }
</style>

<!-- CONTENEDOR PRINCIPAL DEL MENÚ -->
<div class="menu-container">
    <h1><?= $title ?></h1>
    
    <!-- OPCIÓN 1: Modificación de datos de producto -->
    <a href="<?= BASE_URL ?>inventario/editar" class="option">Modificar un Producto</a>
    
    <!-- OPCIÓN 2: Restock entre productos relacionados -->
    <a href="<?= BASE_URL ?>inventario/restock" class="option">Restock de un Producto</a>
    
    <!-- OPCIÓN 3: Reabastecimiento simple de stock -->
    <a href="<?= BASE_URL ?>inventario/reabastecer" class="option">Reabastecer un Producto</a>
</div>

<?php require_once VIEW_PATH . '/layouts/footer.php'; ?>