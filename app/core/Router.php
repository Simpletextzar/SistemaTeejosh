<?php
/**
 * ============================================================================
 * SISTEMA DE ENRUTAMIENTO MVC - FRONT CONTROLLER
 * ============================================================================
 * 
 * ARCHIVO: Router.php
 * UBICACIÓN: /app/core/Router.php
 * 
 * DESCRIPCIÓN:
 * Clase Router que implementa el patrón Front Controller. Analiza las URLs,
 * determina el controlador y método apropiados, y ejecuta la lógica correspondiente.
 * Es el corazón del sistema de enrutamiento MVC.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Parseo inteligente de URLs amigables
 * - Detección automática de controladores y métodos
 * - Manejo de parámetros dinámicos en URLs
 * - Redirección automática basada en estado de autenticación
 * - Carga dinámica de controladores y métodos
 * 
 * DATOS IMPORTANTES:
 * - 🔴 COMPLEJIDAD: Alta
 * - 🎯 RESPONSABILIDAD: Coordinar todo el flujo MVC
 * - 🔄 INSTANCIACIÓN: Automática en index.php
 * - 🚀 EJECUCIÓN: Todo ocurre en el constructor
 * 
 * RELACIONES:
 * - INSTANCIADO POR: public/index.php
 * - USA: Todos los controladores (AuthController, HomeController, etc.)
 * - INTERACTÚA CON: Sesiones PHP para autenticación
 * - CARGA: Archivos de controladores dinámicamente
 * 
 * EJEMPLOS DE RUTEO:
 * 
 * URL: /index.php?url=inventario/create
 * → Controlador: InventarioController
 * → Método: create()
 * → Parámetros: []
 * 
 * URL: /index.php?url=productos/editar/25
 * → Controlador: ProductosController  
 * → Método: editar()
 * → Parámetros: [25]
 * 
 * URL: /index.php?url= (vacía)
 * → Usuario NO autenticado: AuthController->login()
 * → Usuario SÍ autenticado: HomeController->index()
 * 
 * FLUJO COMPLETO DEL ROUTER:
 * 1. Constructor llamado desde index.php
 * 2. parseUrl() extrae y sanitiza la URL
 * 3. Lógica de redirección por autenticación
 * 4. Detección de controlador y método
 * 5. Carga del archivo del controlador
 * 6. Instanciación del controlador
 * 7. Ejecución del método con parámetros
 * 8. Renderizado de vista al usuario
 */

class Router {
    /**
     * Nombre de la clase del controlador a ejecutar
     * @var string $controller Nombre completo del controlador
     * @access private
     */
    private $controller = 'AuthController'; // Por defecto va al login
    
    /**
     * Método del controlador a ejecutar
     * @var string $method Nombre del método a llamar
     * @access private
     */
    private $method = 'login'; // Por defecto muestra login
    
    /**
     * Parámetros para pasar al método del controlador
     * @var array $params Array de parámetros extraídos de la URL
     * @access private
     */
    private $params = [];
    
    /**
     * Constructor - Procesa la URL y ejecuta el controlador/método correspondiente
     * 
     * DESCRIPCIÓN:
     * El constructor hace todo el trabajo de enrutamiento. Al instanciarse:
     * 1. Parsea la URL
     * 2. Determina controlador, método y parámetros
     * 3. Carga y ejecuta el controlador
     * 4. Los resultados se envían al navegador
     * 
     * FLUJO DETALLADO:
     * 
     * PASO 1 - Parseo de URL:
     * - Lee $_GET['url'] y la divide por '/'
     * - Ejemplo: 'inventario/editar/25' → ['inventario', 'editar', '25']
     * 
     * PASO 2 - Lógica de redirección por autenticación:
     * - Si URL vacía Y usuario logueado → HomeController->index()
     * - Si URL vacía Y usuario NO logueado → AuthController->login() (default)
     * 
     * PASO 3 - Detección de controlador:
     * - Toma primer segmento: 'inventario' → 'InventarioController'
     * - Verifica que exista: /app/controllers/InventarioController.php
     * - Si no existe, mantiene controlador por defecto
     * 
     * PASO 4 - Detección de método:
     * - Toma segundo segmento: 'editar' → método 'editar'
     * - Verifica que el método exista en el controlador
     * - Si no existe, usa método por defecto ('index')
     * 
     * PASO 5 - Parámetros:
     * - Segmentos restantes se convierten en parámetros: ['25']
     * 
     * PASO 6 - Ejecución:
     * - Carga el controlador: require_once InventarioController.php
     * - Instancia: new InventarioController()
     * - Ejecuta: $controller->editar(25)
     * 
     * @access public
     */
    public function __construct() {
        // Paso 1: Parsear la URL en segmentos
        $url = $this->parseUrl();
        
        // Paso 2: Lógica para URL vacía (página principal)
        if (empty($url) || (count($url) == 1 && empty($url[0]))) {
            // Si el usuario está autenticado, ir al home
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                $this->controller = 'HomeController';
                $this->method = 'index';
            }
            // Si no está autenticado, se mantiene AuthController->login() (default)
        } else {
            // Paso 3: Determinar controlador desde URL
            // Ejemplo: URL 'inventario' → busca 'InventarioController.php'
            if (isset($url[0]) && file_exists(APP_PATH . '/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
                $this->controller = ucfirst($url[0]) . 'Controller';
                unset($url[0]); // Remover el segmento usado
            }
            // Si no se encuentra el controlador, se mantiene el default (AuthController)
        }
        
        // Paso 4: Cargar e instanciar el controlador
        require_once APP_PATH . '/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;
        
        // Paso 5: Determinar método desde URL
        if (isset($url[1])) {
            // Verificar que el método exista en el controlador
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]); // Remover el segmento usado
            }
            // Si el método no existe, se mantiene el método por defecto
        }
        
        // Paso 6: Los segmentos restantes son parámetros
        $this->params = $url ? array_values($url) : [];
        
        // Paso 7: Ejecutar el controlador y método con parámetros
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
    
    /**
     * Parsea y sanitiza la URL recibida por GET
     * 
     * DESCRIPCIÓN:
     * Extrae la URL del parámetro GET 'url', la divide en segmentos
     * y aplica sanitización para seguridad.
     * 
     * FUNCIONAMIENTO:
     * 1. Verifica que exista $_GET['url']
     * 2. Elimina slash final con rtrim()
     * 3. Divide por '/' para obtener segmentos
     * 4. Aplica FILTER_SANITIZE_URL para seguridad
     * 
     * EJEMPLOS:
     * ```php
     * // URL: /index.php?url=inventario/editar/25
     * $url = $this->parseUrl();
     * // Resultado: ['inventario', 'editar', '25']
     * 
     * // URL: /index.php?url=auth/login
     * $url = $this->parseUrl();
     * // Resultado: ['auth', 'login']
     * 
     * // URL: /index.php (sin parámetro url)
     * $url = $this->parseUrl();
     * // Resultado: [] (array vacío)
     * ```
     * 
     * SEGURIDAD:
     * - FILTER_SANITIZE_URL remueve caracteres ilegales en URL
     * - Previene ataques de directory traversal
     * - Elimina caracteres potencialmente peligrosos
     * 
     * @return array Array de segmentos de la URL sanitizados
     * @access private
     */
    private function parseUrl() {
        if (isset($_GET['url'])) {
            // Dividir URL por slash y sanitizar
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}