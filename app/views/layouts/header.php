<?php
/**
 * ============================================================================
 * CABECERA HTML PRINCIPAL - ESTRUCTURA BASE DEL DOCUMENTO
 * ============================================================================
 * 
 * ARCHIVO: header.php
 * UBICACIÓN: /app/views/layouts/header.php
 * 
 * DESCRIPCIÓN:
 * Componente de layout que define la estructura base HTML de todas las páginas.
 * Incluye metadatos, título dinámico y carga de estilos CSS. Es el punto de
 * inicio de todo documento HTML en el sistema.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Definición del doctype y estructura HTML base
 * - Metadatos esenciales (charset, viewport)
 * - Título dinámico de página con valor por defecto
 * - Carga de hojas de estilo CSS (reset y estilos principales)
 * - Prevención de cache CSS con parámetros de versión
 * 
 * DATOS IMPORTANTES:
 * - 🟢 COMPLEJIDAD: Baja
 * - 🎨 ESTILOS: Carga reset.css y styles.css
 * - 📱 RESPONSIVE: Viewport configurado para dispositivos móviles
 * - 🔄 DINÁMICO: Título de página variable
 * - 🌍 IDIOMA: Español (lang="es")
 * 
 * RELACIONES:
 * - INCLUIDO POR: Todas las vistas principales del sistema
 * - INCLUYE A: navbar.php (generalmente después del body)
 * - CARGA: /public/css/reset.css, /public/css/styles.css
 * - USA: Función asset() para rutas CSS
 * 
 * ESTRUCTURA HTML GENERADA:
 * ```html
 * <!DOCTYPE html>
 * <html lang="es">
 * <head>
 *     <meta charset="UTF-8">
 *     <meta name="viewport" content="width=device-width, initial-scale=1.0">
 *     <title>Título de la Página</title>
 *     <link rel="stylesheet" href="/css/reset.css?v=123456789">
 *     <link rel="stylesheet" href="/css/styles.css?v=123456789">
 * </head>
 * <body>
 * ```
 * 
 * VARIABLES REQUERIDAS:
 * - $title (opcional): Título de la página específica
 *   Si no se proporciona, usa "Sistema de Inventario" por defecto
 * 
 * EJEMPLO DE USO EN VISTAS:
 * ```php
 * <?php require_once VIEW_PATH . '/layouts/header.php'; ?>
 * 
 * <!-- Contenido específico de la vista -->
 * 
 * <?php require_once VIEW_PATH . '/layouts/footer.php'; ?>
 * ```
 * 
 * NOTAS CSS/HTML CRÍTICAS:
 * 
 * 🎨 PALETA DE COLORES (referencia para todo el sistema):
 * - Color principal: #008c9e (azul verde)
 * - Color secundario: #011a00ff (verde oscuro)
 * - Color texto: white (blanco)
 * 
 * 📱 VIEWPORT CONFIG:
 * - width=device-width: Adapta al ancho del dispositivo
 * - initial-scale=1.0: Sin zoom inicial
 * - Habilita diseño responsive
 * 
 * 🔄 PREVENCIÓN DE CACHE CSS:
 * - ?v=<?= time() ?>: Agrega timestamp para forzar recarga
 * - Útil durante desarrollo, en producción usar versión fija
 * 
 * 🏗️ ESTRUCTURA DE CARPETA CSS:
 * - reset.css: Normalización de estilos entre navegadores
 * - styles.css: Estilos personalizados del sistema
 */

// ============================================================================
// SECCIÓN HTML: ESTRUCTURA BASE DEL DOCUMENTO
// ============================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- METADATOS ESENCIALES -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- TÍTULO DINÁMICO DE PÁGINA -->
    <title><?= $title ?? 'Sistema de Inventario' ?></title>
    
    <!-- HOJAS DE ESTILO CSS -->
    <!-- reset.css: Normaliza estilos entre navegadores -->
    <link rel="stylesheet" href="<?= asset('css/reset.css') ?>?v=<?= time() ?>">
    <!-- styles.css: Estilos personalizados del sistema -->
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>?v=<?= time() ?>">
</head>
<body>