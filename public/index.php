<?php
/**
 * ============================================================================
 * PUNTO DE ENTRADA ÚNICO - FRONT CONTROLLER
 * ============================================================================
 * 
 * ARCHIVO: index.php
 * UBICACIÓN: /public/index.php
 * 
 * DESCRIPCIÓN:
 * Front Controller del sistema MVC. Todas las peticiones HTTP pasan por aquí.
 * Implementa el patrón Front Controller para centralizar el manejo de requests.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Inicialización de sesiones de usuario
 * - Carga de configuración del sistema
 * - Inicio del Router para procesamiento de rutas
 * 
 * DATOS IMPORTANTES:
 * - 🟢 COMPLEJIDAD: Baja (pero crítico)
 * - ⚠️  Único archivo PHP accesible directamente desde el navegador
 * - 🔄 Cargado por: Servidor web (Apache/Nginx)
 * - 📦 Carga a: config/config.php
 * 
 * RELACIONES:
 * - CARGADO POR: Servidor Web
 * - CARGA: config/config.php
 * - USA: app/core/Router.php
 * 
 * EJEMPLO DE FLUJO:
 * 1. Usuario → http://localhost/sistema/public/index.php?url=inventario
 * 2. index.php → inicia sesión → carga config.php → crea Router
 * 3. Router → analiza 'inventario' → carga InventarioController
 * 4. Controller → procesa lógica → carga vista → HTML al navegador
 * 
 * NOTAS CSS/HTML:
 * - No genera HTML directamente, solo prepara el entorno
 * - Las vistas se cargan después a través de los controladores
 */


error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../php_errors.log');

// SECCIÓN 1: INICIO DE SESIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SECCIÓN 2: CARGA DE CONFIGURACIÓN
require_once __DIR__ . '/../config/config.php';

// SECCIÓN 3: INICIO DEL ROUTER
$router = new Router();