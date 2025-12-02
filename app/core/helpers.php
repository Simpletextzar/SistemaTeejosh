<?php
/**
 * ============================================================================
 * FUNCIONES AUXILIARES GLOBALES (HELPERS)
 * ============================================================================
 * 
 * ARCHIVO: helpers.php
 * UBICACIÓN: /app/core/helpers.php
 * 
 * DESCRIPCIÓN:
 * Colección de funciones globales utilitarias disponibles en toda la aplicación.
 * Estas funciones simplifican tareas comunes como generación de URLs, manejo
 * de assets, redirecciones y debugging.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Generación de URLs del sistema y assets estáticos
 * - Redirecciones HTTP y obtención de URL actual
 * - Funciones de debugging para desarrollo
 * - Utilidades para vistas y controladores
 * 
 * DATOS IMPORTANTES:
 * - 🟢 COMPLEJIDAD: Baja
 * - 🌍 ALCANCE: Global (disponible en toda la aplicación)
 * - 🔧 UTILIDAD: Simplifica código repetitivo
 * - 🎯 USO PRIMARIO: Vistas y controladores
 * 
 * RELACIONES:
 * - CARGADO POR: config/config.php (al inicio de la aplicación)
 * - USADO POR: Todas las vistas, controladores y modelos
 * - DEPENDE DE: BASE_URL (definida en config.php)
 * 
 * EJEMPLOS DE USO EN VISTAS:
 * ```php
 * <!-- Generar enlace a página de inventario -->
 * <a href="<?= url('inventario') ?>">Ver Inventario</a>
 * 
 * <!-- Incluir archivo CSS -->
 * <link rel="stylesheet" href="<?= asset('/css/styles.css') ?>">
 * 
 * <!-- Incluir archivo JavaScript -->
 * <script src="<?= asset('/js/app.js') ?>"></script>
 * 
 * <!-- Imagen -->
 * <img src="<?= asset('/images/logo.png') ?>" alt="Logo">
 * ```
 * 
 * NOTAS CSS/HTML:
 * - asset() es CRÍTICO para referenciar correctamente CSS, JS e imágenes
 * - url() genera enlaces que mantienen la estructura MVC del sistema
 * - Las funciones retornan strings listos para usar en atributos HTML
 */

/**
 * Genera URL completa para rutas del sistema MVC
 * 
 * DESCRIPCIÓN:
 * Construye URLs que siguen la estructura del front controller (index.php?url=).
 * Es la forma correcta de generar enlaces dentro de la aplicación MVC.
 * 
 * PARÁMETROS:
 * @param string $path Ruta del controlador/método (ej: 'inventario/create')
 * 
 * RETORNA:
 * @return string URL completa lista para usar en href o redirect
 * 
 * EJEMPLOS:
 * ```php
 * // URL básica
 * echo url('inventario'); 
 * // Resultado: /sistema/public/index.php?url=inventario
 * 
 * // URL con método
 * echo url('auth/login');
 * // Resultado: /sistema/public/index.php?url=auth/login
 * 
 * // URL vacía (página principal)
 * echo url();
 * // Resultado: /sistema/public/index.php?url=
 * 
 * // Uso en HTML
 * <a href="<?= url('productos/editar/' . $id) ?>">Editar</a>
 * ```
 * 
 * FUNCIONAMIENTO:
 * 1. Recibe: 'inventario/create'
 * 2. Trim: 'inventario/create' (sin cambios)
 * 3. Concatena: BASE_URL + 'inventario/create'
 * 4. Retorna: '/sistema/public/index.php?url=inventario/create'
 */
function url($path = '') {
    // Eliminar las barras iniciales y finales del path
    $path = trim($path, '/');
    return BASE_URL . $path;
}

/**
 * Genera URL para archivos estáticos (CSS, JS, imágenes)
 * 
 * DESCRIPCIÓN:
 * Construye rutas absolutas a archivos en el directorio /public.
 * Esencial para cargar correctamente CSS, JavaScript, imágenes y otros assets.
 * 
 * PARÁMETROS:
 * @param string $path Ruta al archivo estático (ej: '/css/styles.css')
 * 
 * RETORNA:
 * @return string URL completa al asset
 * 
 * EJEMPLOS:
 * ```php
 * // Archivo CSS
 * echo asset('/css/styles.css');
 * // Resultado: /sistema/public/css/styles.css
 * 
 * // Archivo JavaScript
 * echo asset('/js/app.js');
 * // Resultado: /sistema/public/js/app.js
 * 
 * // Imagen
 * echo asset('/images/logo.png');
 * // Resultado: /sistema/public/images/logo.png
 * 
 * // Uso en HTML
 * <link rel="stylesheet" href="<?= asset('/css/styles.css') ?>">
 * <script src="<?= asset('/js/script.js') ?>"></script>
 * <img src="<?= asset('/images/banner.jpg') ?>" alt="Banner">
 * ```
 * 
 * FUNCIONAMIENTO:
 * 1. Detecta ruta base del proyecto desde $_SERVER['SCRIPT_NAME']
 * 2. Normaliza barras (Windows vs Linux)
 * 3. Asegura que el path comience con '/'
 * 4. Retorna ruta absoluta al asset
 * 
 * NOTAS CSS/HTML:
 * ⚠️ CRÍTICO: Usar SIEMPRE asset() para CSS, JS e imágenes
 * ⚠️ SIN asset(): Los archivos no se cargarán correctamente
 * ✅ CON asset(): Rutas consistentes en cualquier entorno
 */
function asset($path = '') {
    // Detectar la ruta base del proyecto
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $baseAssetPath = rtrim($scriptDir, '/');
    
    // Si el path no empieza con /, agregarlo
    if (!empty($path) && $path[0] !== '/') {
        $path = '/' . $path;
    }
    
    return $baseAssetPath . $path;
}

/**
 * Redirige a una URL del sistema y termina ejecución
 * 
 * DESCRIPCIÓN:
 * Realiza redirección HTTP usando la función url() para generar la URL destino.
 * Similar a Controller::redirect() pero disponible globalmente.
 * 
 * PARÁMETROS:
 * @param string $path Ruta destino (ej: 'auth/login')
 * 
 * EJEMPLOS:
 * ```php
 * // Redirigir al login
 * redirect('auth/login');
 * 
 * // Redirigir al home
 * redirect('home');
 * 
 * // Redirigir después de éxito
 * if ($operacionExitosa) {
 *     redirect('inventario?mensaje=success');
 * }
 * ```
 * 
 * USO TÍPICO:
 * - Después de procesar formularios
 * - Cuando falla la autenticación
 * - Para prevenir reenvío de forms (POST-REDIRECT-GET)
 * - En scripts simples fuera del patrón MVC
 */
function redirect($path = '') {
    header('Location: ' . url($path));
    exit;
}

/**
 * Obtiene la URL actual solicitada por el cliente
 * 
 * DESCRIPCIÓN:
 * Retorna la URL completa que el usuario ingresó en el navegador.
 * Útil para logging, analytics, o condicionales basados en la URL.
 * 
 * RETORNA:
 * @return string URL actual completa
 * 
 * EJEMPLOS:
 * ```php
 * // Obtener URL actual
 * $current = current_url();
 * // Ejemplo: "/sistema/public/index.php?url=inventario/create"
 * 
 * // Usar en condicional
 * if (strpos(current_url(), 'admin') !== false) {
 *     echo "Estás en área administrativa";
 * }
 * 
 * // Logging
 * error_log("Acceso a: " . current_url());
 * ```
 */
function current_url() {
    return $_SERVER['REQUEST_URI'];
}

/**
 * Función de debugging: Imprime variable y termina ejecución
 * 
 * DESCRIPCIÓN:
 * "Dump and Die" - Imprime una variable de forma legible y detiene la ejecución.
 * Exclusivamente para desarrollo y debugging.
 * 
 * PARÁMETROS:
 * @param mixed $data Variable a inspeccionar
 * 
 * EJEMPLOS:
 * ```php
 * // Debug de variable simple
 * dd($usuario);
 * 
 * // Debug de array
 * dd(['nombre' => 'Juan', 'edad' => 25]);
 * 
 * // Debug en medio de lógica
 * $resultado = algunaFuncion();
 * dd($resultado); // Ver qué retorna y terminar
 * 
 * // Output ejemplo:
 * // array(2) {
 * //   ["nombre"]=> string(4) "Juan"
 * //   ["edad"]=> int(25)
 * // }
 * ```
 * 
 * ADVERTENCIAS:
 * ⚠️ NUNCA usar en producción (revela información sensible)
 * ⚠️ Remover todas las llamadas dd() antes de deploy
 * ✅ Solo para desarrollo local y debugging
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}