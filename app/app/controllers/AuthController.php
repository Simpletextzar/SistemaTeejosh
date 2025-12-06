<?php
/**
 * ============================================================================
 * CONTROLADOR DE AUTENTICACIÓN - MANEJO DE LOGIN Y LOGOUT
 * ============================================================================
 * 
 * ARCHIVO: AuthController.php
 * UBICACIÓN: /app/controllers/AuthController.php
 * 
 * DESCRIPCIÓN:
 * Controlador especializado en gestionar el proceso de autenticación de usuarios.
 * Maneja el inicio y cierre de sesión, redirecciones por estado de autenticación,
 * y validación de credenciales.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Presentación del formulario de login
 * - Validación de credenciales de usuario
 * - Creación y destrucción de sesiones
 * - Redirección automática basada en estado de autenticación
 * - Manejo de errores de autenticación
 * 
 * DATOS IMPORTANTES:
 * - 🟢 COMPLEJIDAD: Baja-Medio
 * - 🔐 SEGURIDAD: Manejo de sesiones PHP
 * - 📦 HERENCIA: Extiende de Controller.php
 * - 🎯 ALCANCE: Proceso completo de autenticación
 * 
 * RELACIONES:
 * - HEREDA DE: Controller.php (acceso a métodos base)
 * - USA: User.php (modelo de autenticación)
 * - CARGA: auth/login.php (vista de login)
 * - RUTAS: /auth/login, /auth/logout
 * 
 * FLUJO DE AUTENTICACIÓN:
 * 
 * 1. Usuario accede a /auth/login
 * 2. AuthController->login() verifica si ya está autenticado
 * 3. Si SÍ está autenticado → Redirige a /home/index
 * 4. Si NO está autenticado → Muestra formulario login
 * 5. Usuario envía credenciales (POST)
 * 6. AuthController valida y autentica con User model
 * 7. Si credenciales válidas → Crea sesión → Redirige a home
 * 8. Si credenciales inválidas → Muestra error → Vuelve a formulario
 * 
 * EJEMPLOS DE USO:
 * 
 * ```php
 * // URL: /auth/login (GET) - Muestra formulario
 * // URL: /auth/login (POST) - Procesa login
 * // URL: /auth/logout - Cierra sesión
 * ```
 * 
 * ESTRUCTURA DE DATOS DE SESIÓN:
 * - $_SESSION['loggedin'] = true
 * - $_SESSION['user_id'] = 1
 * - $_SESSION['username'] = 'teejosh'
 * - $_SESSION['role'] = 'admin'
 */

class AuthController extends Controller {
    
    /**
     * Instancia del modelo User para operaciones de autenticación
     * @var User $userModel
     * @access private
     */
    private $userModel;
    
    /**
     * Constructor - Inicializa el modelo de usuario
     * 
     * DESCRIPCIÓN:
     * Carga el modelo User que se utilizará para todas las operaciones
     * de autenticación en este controlador.
     * 
     * FLUJO:
     * 1. Llama a parent::__construct() (Controller base)
     * 2. Carga modelo User mediante $this->model('User')
     * 3. Asigna la instancia a $this->userModel
     * 
     * @access public
     */
    public function __construct() {
        $this->userModel = $this->model('User');
    }
    
    /**
     * Maneja el proceso completo de login (GET y POST)
     * 
     * DESCRIPCIÓN:
     * Método principal que maneja tanto la visualización del formulario
     * de login (GET) como el procesamiento de credenciales (POST).
     * Incluye redirección automática para usuarios ya autenticados.
     * 
     * FLUJO DETALLADO:
     * 
     * CASO 1: Usuario YA autenticado
     * 1. Verifica $this->isAuthenticated()
     * 2. Si true → Redirige a /home/index
     * 3. Termina ejecución
     * 
     * CASO 2: Petición GET (mostrar formulario)
     * 1. Prepara $data con error vacío
     * 2. Carga vista auth/login.php con $data
     * 3. Usuario ve formulario
     * 
     * CASO 3: Petición POST (procesar login)
     * 1. Obtiene y sanitiza username/password
     * 2. Valida campos no vacíos
     * 3. Intenta autenticar con User model
     * 4. Si éxito → Crea sesión → Redirige a home
     * 5. Si falla → Prepara mensaje error → Muestra formulario
     * 
     * PARÁMETROS POST ESPERADOS:
     * - username (string): Nombre de usuario
     * - password (string): Contraseña
     * 
     * VISTA ASOCIADA:
     * - auth/login.php (formulario de autenticación)
     * 
     * EJEMPLO DE USO:
     * ```php
     * // Acceso vía navegador
     * // GET: http://localhost/sistema/public/index.php?url=auth/login
     * // POST: Mismo URL con datos de formulario
     * ```
     * 
     * @access public
     */
    public function login() {
        // CASO 1: Usuario ya autenticado - redirigir al home
        if ($this->isAuthenticated()) {
            $this->redirect('home/index');
        }
        
        // Preparar datos para la vista
        $data = [
            'error' => '' // Mensaje de error inicialmente vacío
        ];
        
        // CASO 3: Procesar formulario de login (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener y limpiar datos del formulario
            $username = trim($this->post('username'));
            $password = trim($this->post('password'));
            
            // Validar que los campos no estén vacíos
            if (empty($username) || empty($password)) {
                $data['error'] = 'Por favor complete todos los campos';
            } else {
                // Intentar autenticar usuario
                $user = $this->userModel->authenticate($username, $password);
                
                if ($user) {
                    // AUTENTICACIÓN EXITOSA
                    // Crear sesión de usuario
                    $this->userModel->createSession($user);
                    // Redirigir a página principal
                    $this->redirect('home/index');
                } else {
                    // CREDENCIALES INVÁLIDAS
                    $data['error'] = 'Usuario o contraseña incorrectos';
                }
            }
        }
        
        // CASO 2: Mostrar formulario de login (GET o POST con error)
        $this->view('auth/login', $data);
    }
    
    /**
     * Cierra la sesión del usuario y redirige al login
     * 
     * DESCRIPCIÓN:
     * Destruye completamente la sesión del usuario y redirige
     * a la página de login. Operación siempre exitosa.
     * 
     * FLUJO:
     * 1. Llama a $this->userModel->destroySession()
     * 2. Destruye todas las variables de sesión
     * 3. Redirige a /auth/login
     * 4. Termina ejecución
     * 
     * EJEMPLO DE USO:
     * ```php
     * // Acceso vía navegador o enlace
     * // http://localhost/sistema/public/index.php?url=auth/logout
     * 
     * // En vista: <a href="<?= url('auth/logout') ?>">Cerrar Sesión</a>
     * ```
     * 
     * @access public
     */
    public function logout() {
        $this->userModel->destroySession();
        $this->redirect('auth/login');
    }
}