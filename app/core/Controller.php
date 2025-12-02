<?php
/**
 * ============================================================================
 * CLASE BASE PARA CONTROLADORES MVC
 * ============================================================================
 * 
 * ARCHIVO: Controller.php
 * UBICACIÓN: /app/core/Controller.php
 * 
 * DESCRIPCIÓN:
 * Clase abstracta base que todos los controladores del sistema heredan.
 * Proporciona funcionalidades comunes para manejo de vistas, modelos,
 * autenticación, y seguridad.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Carga de modelos y vistas
 * - Redirecciones y respuestas JSON
 * - Autenticación y autorización de usuarios
 * - Sanitización de datos y protección CSRF
 * - Manejo seguro de datos POST/GET
 * 
 * DATOS IMPORTANTES:
 * - 🟡 COMPLEJIDAD: Media-Alta
 * - 📦 HERENCIA: Todos los controladores heredan de esta clase
 * - 🔐 SEGURIDAD: Incluye sanitización, CSRF, y validación de auth
 * - 🎯 RESPONSABILIDAD: Coordinar entre modelos y vistas
 * 
 * RELACIONES:
 * - HEREDADA POR: AuthController, HomeController, InventarioController
 * - USA: Model.php (para cargar modelos), VIEW_PATH (para vistas)
 * - INTERACTÚA CON: Sesiones PHP ($_SESSION), Datos HTTP ($_POST, $_GET)
 * 
 * EJEMPLO DE USO EN CONTROLADOR HIJO:
 * ```php
 * class HomeController extends Controller {
 *     public function index() {
 *         // Verificar autenticación
 *         $this->requireAuth();
 *         
 *         // Cargar modelo
 *         $inventario = $this->model('Inventario');
 *         $productos = $inventario->getAll();
 *         
 *         // Cargar vista con datos
 *         $this->view('home/index', ['productos' => $productos]);
 *     }
 * }
 * ```
 * 
 * CICLO DE VIDA DE UN CONTROLADOR:
 * 1. Router detecta URL → Crea instancia del controlador
 * 2. Controlador ejecuta método específico (ej: index())
 * 3. Método usa $this->model() para acceder a datos
 * 4. Método usa $this->view() para renderizar HTML
 * 5. Vista se envía al cliente
 * 
 * NOTAS CSS/HTML:
 * - No genera HTML directamente, solo prepara datos para vistas
 * - Las vistas se cargan con extract() para convertir arrays en variables
 * - Las redirecciones usan BASE_URL para URLs consistentes
 */

class Controller {
    
    /**
     * Carga y retorna una instancia de modelo
     * 
     * DESCRIPCIÓN:
     * Carga dinámicamente un archivo de modelo y retorna una instancia.
     * Los modelos se ubican en /app/models/ y siguen convención PascalCase.
     * 
     * PARÁMETROS:
     * @param string $model Nombre del modelo (sin .php, ej: 'User')
     * 
     * RETORNA:
     * @return object Instancia del modelo solicitado
     * 
     * @throws Exception Si el archivo del modelo no existe
     * 
     * EJEMPLOS:
     * ```php
     * // Cargar modelo User
     * $userModel = $this->model('User');
     * 
     * // Cargar modelo Inventario
     * $inventarioModel = $this->model('Inventario');
     * 
     * // Usar el modelo
     * $usuarios = $userModel->getAll();
     * ```
     * 
     * FLUJO INTERNO:
     * 1. Construye ruta: APP_PATH . '/models/User.php'
     * 2. Verifica si archivo existe
     * 3. Require_once del archivo
     * 4. Retorna new User() (que hereda de Model)
     * 
     * @access protected
     */
    protected function model($model) {
        $modelPath = APP_PATH . '/models/' . $model . '.php';
        
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        }
        
        die("Modelo {$model} no encontrado");
    }
    
    /**
     * Carga y renderiza una vista con datos
     * 
     * DESCRIPCIÓN:
     * Carga un archivo de vista y extrae los datos para que estén disponibles
     * como variables en el scope de la vista.
     * 
     * PARÁMETROS:
     * @param string $view Ruta de la vista (ej: 'home/index')
     * @param array $data Datos a pasar a la vista
     * 
     * @throws Exception Si el archivo de vista no existe
     * 
     * EJEMPLOS:
     * ```php
     * // Vista sin datos
     * $this->view('home/index');
     * 
     * // Vista con datos
     * $this->view('inventario/listado', [
     *     'productos' => $productos,
     *     'titulo' => 'Lista de Productos'
     * ]);
     * 
     * // En la vista (inventario/listado.php):
     * // echo $titulo; // "Lista de Productos"
     * // foreach ($productos as $producto) ...
     * ```
     * 
     * FUNCIONAMIENTO DE extract():
     * Convierte: ['productos' => $data, 'titulo' => 'Texto']
     * En variables: $productos = $data, $titulo = 'Texto'
     * 
     * @access protected
     */
    protected function view($view, $data = []) {
        // Extraer datos a variables individuales
        extract($data);
        
        $viewPath = VIEW_PATH . '/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("Vista {$view} no encontrada");
        }
    }
    
    /**
     * Redirige a otra URL del sistema
     * 
     * DESCRIPCIÓN:
     * Realiza redirección HTTP usando la URL base del sistema.
     * Termina la ejecución del script inmediatamente después.
     * 
     * PARÁMETROS:
     * @param string $url Ruta destino (ej: 'auth/login', 'inventario')
     * 
     * EJEMPLOS:
     * ```php
     * // Redirigir al login
     * $this->redirect('auth/login');
     * 
     * // Redirigir al listado de inventario
     * $this->redirect('inventario');
     * 
     * // URL resultante: /sistema/public/index.php?url=auth/login
     * ```
     * 
     * @access protected
     */
    protected function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit;
    }
    
    /**
     * Verifica si el usuario está autenticado
     * 
     * DESCRIPCIÓN:
     * Comprueba si existe una sesión de usuario activa y válida.
     * 
     * RETORNA:
     * @return bool true si está autenticado, false si no
     * 
     * EJEMPLO:
     * ```php
     * if ($this->isAuthenticated()) {
     *     echo "Bienvenido usuario";
     * } else {
     *     echo "Por favor inicia sesión";
     * }
     * ```
     * 
     * @access protected
     */
    protected function isAuthenticated() {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    }
    
    /**
     * Fuerza autenticación - redirige al login si no está autenticado
     * 
     * DESCRIPCIÓN:
     * Middleware de autenticación. Si el usuario no está logueado,
     * redirige automáticamente a la página de login.
     * 
     * EJEMPLO:
     * ```php
     * public function dashboard() {
     *     $this->requireAuth(); // Si no está logueado, va al login
     *     
     *     // El resto del código solo ejecuta si está autenticado
     *     $this->view('user/dashboard');
     * }
     * ```
     * 
     * @access protected
     */
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->redirect('auth/login');
        }
    }
    
    /**
     * Obtiene dato POST de forma segura con valor por defecto
     * 
     * DESCRIPCIÓN:
     * Accede seguro a datos POST con fallback a valor por defecto.
     * Previene errores de "undefined index".
     * 
     * PARÁMETROS:
     * @param string $key Clave del dato POST
     * @param mixed $default Valor por defecto si no existe
     * 
     * RETORNA:
     * @return mixed Valor POST o valor por defecto
     * 
     * EJEMPLOS:
     * ```php
     * $nombre = $this->post('nombre', 'Anónimo');
     * $email = $this->post('email'); // null si no existe
     * 
     * // Equivalente a:
     * $nombre = $_POST['nombre'] ?? 'Anónimo';
     * ```
     * 
     * @access protected
     */
    protected function post($key, $default = null) {
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Obtiene dato GET de forma segura con valor por defecto
     * 
     * PARÁMETROS:
     * @param string $key Clave del dato GET
     * @param mixed $default Valor por defecto si no existe
     * 
     * RETORNA:
     * @return mixed Valor GET o valor por defecto
     * 
     * EJEMPLO:
     * ```php
     * $pagina = $this->get('pagina', 1);
     * $busqueda = $this->get('q', '');
     * ```
     * 
     * @access protected
     */
    protected function get($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Retorna respuesta JSON y termina ejecución
     * 
     * DESCRIPCIÓN:
     * Envía datos como respuesta JSON con código HTTP personalizable.
     * Útil para APIs o respuestas AJAX.
     * 
     * PARÁMETROS:
     * @param mixed $data Datos a convertir a JSON
     * @param int $statusCode Código HTTP (200, 404, 500, etc.)
     * 
     * EJEMPLOS:
     * ```php
     * // Respuesta exitosa
     * $this->json(['success' => true, 'data' => $productos]);
     * 
     * // Respuesta de error
     * $this->json(['error' => 'Producto no encontrado'], 404);
     * 
     * // En el cliente JavaScript:
     * // fetch('/api/productos').then(r => r.json()).then(data => ...)
     * ```
     * 
     * @access protected
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Sanitiza entrada de usuario para prevenir XSS
     * 
     * DESCRIPCIÓN:
     * Limpia strings o arrays de datos eliminando tags HTML y escapando
     * caracteres especiales. Protección básica contra XSS.
     * 
     * PARÁMETROS:
     * @param mixed $data String o array a sanitizar
     * 
     * RETORNA:
     * @return mixed Datos sanitizados
     * 
     * EJEMPLOS:
     * ```php
     * // Sanitizar string
     * $nombre = $this->sanitize("<script>alert('xss')</script>Juan");
     * // Resultado: "Juan" (sin tags script)
     * 
     * // Sanitizar array
     * $datos = $this->sanitize([
     *     'nombre' => '<b>Juan</b>',
     *     'email' => 'juan@example.com'
     * ]);
     * ```
     * 
     * @access protected
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Genera y almacena token CSRF
     * 
     * DESCRIPCIÓN:
     * Crea token único para protección contra Cross-Site Request Forgery.
     * El token se almacena en sesión y debe incluirse en forms.
     * 
     * RETORNA:
     * @return string Token CSRF generado
     * 
     * EJEMPLO:
     * ```php
     * // En el controlador:
     * $token = $this->generateCsrfToken();
     * 
     * // En la vista:
     * <input type="hidden" name="csrf_token" value="<?= $token ?>">
     * ```
     * 
     * @access protected
     */
    protected function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verifica token CSRF
     * 
     * DESCRIPCIÓN:
     * Compara token recibido con token almacenado en sesión.
     * Previene ataques CSRF en forms y acciones sensibles.
     * 
     * PARÁMETROS:
     * @param string $token Token a verificar
     * 
     * RETORNA:
     * @return bool true si el token es válido, false si no
     * 
     * EJEMPLO:
     * ```php
     * public function procesarForm() {
     *     $token = $this->post('csrf_token');
     *     
     *     if (!$this->verifyCsrfToken($token)) {
     *         die("Token CSRF inválido");
     *     }
     *     
     *     // Procesar form seguro...
     * }
     * ```
     * 
     * @access protected
     */
    protected function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}