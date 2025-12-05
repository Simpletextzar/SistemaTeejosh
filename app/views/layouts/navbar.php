<?php
/**
 * ============================================================================
 * BARRA DE NAVEGACIÓN PRINCIPAL - MENÚ SUPERIOR
 * ============================================================================
 * 
 * ARCHIVO: navbar.php
 * UBICACIÓN: /app/views/layouts/navbar.php
 * 
 * DESCRIPCIÓN:
 * Componente de layout que proporciona la barra de navegación superior
 * con el branding del sistema y enlaces de navegación principales.
 * Implementa estilos inline para máxima portabilidad.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Branding del sistema ("Inventario Coleccionables")
 * - Navegación principal (Inicio, Cerrar Sesión)
 * - Estilos visuales consistentes con la identidad del sistema
 * - Efectos hover en botones de navegación
 * - Diseño flexible (flexbox) para alineación
 * 
 * DATOS IMPORTANTES:
 * - 🟢 COMPLEJIDAD: Baja
 * - 🎨 ESTILOS: CSS inline (no depende de archivos externos)
 * - 📱 RESPONSIVE: Diseño flexible con flexbox
 * - 🔗 ENLACES: Usa función url() para rutas MVC
 * - 🎯 USUARIO: Asume usuario autenticado (logout disponible)
 * 
 * RELACIONES:
 * - INCLUIDO POR: Vistas que requieren navegación (home, inventario)
 * - POSICIÓN: Justo después de <body> en header.php
 * - USA: Función url() para generar enlaces MVC
 * 
 * ESTRUCTURA HTML GENERADA:
 * ```html
 * <nav style="...">
 *     <div>Inventario Coleccionables</div>
 *     <div>
 *         <a href="/inicio">Inicio</a>
 *         <a href="/logout">Cerrar Sesión</a>
 *     </div>
 * </nav>
 * ```
 * 
 * RUTAS MVC UTILIZADAS:
 * - home/index: Página principal del sistema
 * - auth/logout: Cierre de sesión
 * 
 * NOTAS CSS/HTML DETALLADAS:
 * 
 * 🎨 PALETA DE COLORES IMPLEMENTADA:
 * 
 * COLOR PRINCIPAL (fondo navbar):
 * - #008c9e (azul verde/agua)
 * - Usado en: background-color del nav
 * - Propósito: Identidad visual principal
 * 
 * COLOR SECUNDARIO (botones):
 * - #011a00ff (verde muy oscuro)
 * - Usado en: background-color de botones
 * - Hover: #010f00ff (verde ligeramente más claro)
 * - Propósito: Contraste y accesibilidad
 * 
 * COLOR DE TEXTO:
 * - white (blanco)
 * - Usado en: color del texto
 * - Propósito: Legibilidad sobre fondos oscuros
 * 
 * 🎯 PROPIEDADES CSS PRINCIPALES:
 * 
 * DISEÑO FLEXIBLE:
 * - display: flex → Layout flexible
 * - justify-content: space-between → Espaciado máximo
 * - align-items: center → Centrado vertical
 * 
 * ESTÉTICA BOTONES:
 * - padding: 8px 14px → Espaciado interno
 * - border-radius: 5px → Esquinas redondeadas
 * - text-decoration: none → Sin subrayado
 * - transition: background 0.3s → Transición suave hover
 * 
 * EFECTOS HOVER:
 * - onmouseover: Cambia background-color a #010f00ff
 * - onmouseout: Restaura background-color a #011a00ff
 * - Feedback visual para interacción usuario
 * 
 * TYPOGRAFÍA:
 * - font-family: Arial, sans-serif → Fuente segura
 * - font-weight: bold → Branding destacado
 * - font-size: 18px → Título visible
 * 
 * USO EN VISTAS:
 * ```php
 * // En una vista específica
 * <?php require_once VIEW_PATH . '/layouts/header.php'; ?>
 * <?php require_once VIEW_PATH . '/layouts/navbar.php'; ?>
 * 
 * <main>
 *     <!-- Contenido específico de la vista -->
 * </main>
 * 
 * <?php require_once VIEW_PATH . '/layouts/footer.php'; ?>
 * ```
 */

// ============================================================================
// SECCIÓN HTML: BARRA DE NAVEGACIÓN CON ESTILOS INLINE
// ============================================================================
?>
<nav style="
    /* FONDO Y COLOR PRINCIPAL */
    background-color: #008c9e;
    color: white;
    
    /* ESPACIADO Y DISEÑO */
    padding: 10px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    
    /* TIPOGRAFÍA */
    font-family: Arial, sans-serif;
">
    <!-- ======================================================================
         SECCIÓN IZQUIERDA: BRANDING DEL SISTEMA
         ====================================================================== -->
    <div style="font-weight: bold; font-size: 18px;">
        Inventario Coleccionables
    </div>

    <!-- ======================================================================
         SECCIÓN DERECHA: BOTONES DE NAVEGACIÓN
         ====================================================================== -->
    <div style="display: flex; gap: 10px;">
        <!-- BOTÓN INICIO: Redirige a la página principal -->
        <a href="<?= url('home/index') ?>" style="
            /* ESTILOS BASE DEL BOTÓN */
            color: white;
            text-decoration: none;
            background-color: #011a00ff;
            padding: 8px 14px;
            border-radius: 5px;
            
            /* TRANSICIÓN PARA EFECTO HOVER */
            transition: background 0.3s;
        " 
        /* EFECTOS HOVER - CAMBIO DE COLOR */
        onmouseover="this.style.backgroundColor='#010f00ff'" 
        onmouseout="this.style.backgroundColor='#011a00ff'">
            Inicio
        </a>

        <!-- BOTÓN CERRAR SESIÓN: Finaliza la sesión del usuario -->
        <a href="<?= url('auth/logout') ?>" style="
            /* ESTILOS IDÉNTICOS AL BOTÓN INICIO */
            color: white;
            text-decoration: none;
            background-color: #011a00ff;
            padding: 8px 14px;
            border-radius: 5px;
            transition: background 0.3s;
        " 
        /* EFECTOS HOVER CONSISTENTES */
        onmouseover="this.style.backgroundColor='#010f00ff'" 
        onmouseout="this.style.backgroundColor='#011a00ff'">
            Cerrar Sesión
        </a>
    </div>
</nav>