<?php
/**
 * ============================================================================
 * CIERRE DE DOCUMENTO HTML - ESTRUCTURA FINAL
 * ============================================================================
 * 
 * ARCHIVO: footer.php
 * UBICACIÓN: /app/views/layouts/footer.php
 * 
 * DESCRIPCIÓN:
 * Componente de layout que cierra la estructura HTML iniciada en header.php.
 * Proporciona el cierre limpio de las etiquetas body y html, marcando el
 * final de todo documento HTML en el sistema.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Cierre de la etiqueta <body> abierta en header.php
 * - Cierre de la etiqueta <html> abierta en header.php
 * - Punto final limpio para todo documento HTML
 * - Lugar para incluir scripts JavaScript antes del cierre
 * 
 * DATOS IMPORTANTES:
 * - 🟢 COMPLEJIDAD: Mínima
 * - 🏗️ ESTRUCTURA: Cierre de etiquetas HTML
 * - 📝 PROPÓSITO: Completar estructura HTML válida
 * - 🔧 MANTENIMIENTO: Raramente necesita cambios
 * 
 * RELACIONES:
 * - COMPLEMENTA: header.php (cierra lo que header abre)
 * - INCLUIDO POR: Todas las vistas principales del sistema
 * - POSICIÓN: Final de todo documento HTML
 * 
 * ESTRUCTURA HTML GENERADA:
 * ```html
 * </body>
 * </html>
 * ```
 * 
 * UBICACIÓN TÍPICA EN VISTAS:
 * ```php
 * <!-- header.php abre <html> y <body> -->
 * <?php require_once VIEW_PATH . '/layouts/header.php'; ?>
 * 
 * <!-- navbar.php proporciona navegación -->
 * <?php require_once VIEW_PATH . '/layouts/navbar.php'; ?>
 * 
 * <!-- Contenido específico de la vista -->
 * <main>
 *     <h1><?= $title ?></h1>
 *     <p>Contenido de la página...</p>
 * </main>
 * 
 * <!-- footer.php cierra <body> y <html> -->
 * <?php require_once VIEW_PATH . '/layouts/footer.php'; ?>
 * ```
 * 
 * NOTAS DE USO AVANZADO:
 * 
 * 📝 INCLUSIÓN DE SCRIPTS JAVASCRIPT:
 * Este es el lugar ideal para incluir scripts JS antes del cierre de body:
 * ```html
 * <!-- Antes del footer.php en la vista -->
 * <script src="<?= asset('js/app.js') ?>"></script>
 * 
 * <!-- Luego incluir footer.php -->
 * <?php require_once VIEW_PATH . '/layouts/footer.php'; ?>
 * ```
 * 
 * 🎨 ESTRUCTURA HTML VÁLIDA:
 * Garantiza que todo documento HTML tenga:
 * - <!DOCTYPE html>
 * - <html> con etiqueta de cierre
 * - <head> completo
 * - <body> con etiqueta de cierre
 * 
 * 🔧 BUENAS PRÁCTICAS:
 * - Siempre incluir footer.php para HTML válido
 * - No agregar contenido después del footer
 * - Mantener este archivo mínimo y limpio
 * 
 * EJEMPLO DE FLUJO COMPLETO:
 * ```php
 * // 1. header.php: <html><head>...</head><body>
 * // 2. navbar.php: <nav>...</nav>
 * // 3. Vista: <main>Contenido</main>
 * // 4. footer.php: </body></html>
 * ```
 */

// ============================================================================
// SECCIÓN HTML: CIERRE DE ESTRUCTURA DOCUMENTO
// ============================================================================
?>
</body>
</html>