<?php
/**
 * ============================================================================
 * CONFIGURACIÓN PRINCIPAL DEL SISTEMA
 * ============================================================================
 * 
 * ARCHIVO: config.php
 * UBICACIÓN: /config/config.php
 * 
 * DESCRIPCIÓN:
 * Archivo central de configuración del sistema. Define constantes, rutas,
 * manejo de errores y carga componentes esenciales.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Definición de rutas del sistema (BASE_PATH, APP_PATH, etc.)
 * - Configuración de zona horaria y manejo de errores
 * - Implementación de autoloader PSR-4 simplificado
 * - Carga de helpers y configuración de base de datos
 * 
 * DATOS IMPORTANTES:
 * - 🔴 COMPLEJIDAD: Avanzada
 * - ⚠️  DEBUG_MODE debe ser FALSE en producción
 * - 🔧 Configuración sensible (rutas, errores, BD)
 * 
 * RELACIONES:
 * - CARGADO POR: public/index.php
 * - CARGA: app/core/helpers.php, config/database.php
 * - USA: Define constantes usadas en toda la aplicación
 * 
 * EJEMPLOS DE CONSTANTES:
 * - BASE_PATH: "/var/www/sistema" (ruta absoluta del proyecto)
 * - BASE_URL: "/sistema/public/index.php?url=" (URL base para enlaces)
 * - VIEW_PATH: "/var/www/sistema/app/views" (ruta a vistas)
 * 
 * NOTAS DE SEGURIDAD:
 * ⚠️  Nunca exponer DEBUG_MODE=true en producción
 * ⚠️  Las rutas absolutas no deben ser accesibles desde web
 */

// Configuración de zona horaria
date_default_timezone_set('America/Lima');

// Definición de rutas del sistema
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('VIEW_PATH', APP_PATH . '/views');

// Configuración de URL base
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('BASE_URL', $scriptDir . '/index.php?url=');

// Configuración de manejo de errores
define('DEBUG_MODE', true);
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Autoloader PSR-4 simplificado
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/core/' . $class . '.php',
        APP_PATH . '/controllers/' . $class . '.php',
        APP_PATH . '/models/' . $class . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Carga de componentes esenciales
require_once APP_PATH . '/core/helpers.php';
require_once BASE_PATH . '/config/database.php';