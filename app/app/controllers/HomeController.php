<?php
/**
 * ============================================================================
 * CONTROLADOR DE PÁGINA PRINCIPAL - MENÚ Y DASHBOARD
 * ============================================================================
 * 
 * ARCHIVO: HomeController.php
 * UBICACIÓN: /app/controllers/HomeController.php
 * 
 * DESCRIPCIÓN:
 * Controlador que maneja la página principal del sistema después del login.
 * Proporciona el menú de navegación y dashboard inicial para usuarios autenticados.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Página de inicio post-autenticación
 * - Verificación obligatoria de autenticación
 * - Presentación del menú principal del sistema
 * - Mostrar información del usuario logueado
 * 
 * DATOS IMPORTANTES:
 * - 🟢 COMPLEJIDAD: Baja
 * - 🔐 SEGURIDAD: Requiere autenticación
 * - 📦 HERENCIA: Extiende de Controller.php
 * - 🎯 ALCANCE: Página principal del sistema
 * 
 * RELACIONES:
 * - HEREDA DE: Controller.php
 * - CARGA: home/index.php (vista principal)
 * - RUTAS: /home/index (ruta por defecto post-login)
 * 
 * FLUJO DE ACCESO:
 * 
 * 1. Usuario se autentica exitosamente
 * 2. Sistema redirige a /home/index
 * 3. HomeController->__construct() verifica autenticación
 * 4. Si NO autenticado → Redirige a login
 * 5. Si autenticado → Ejecuta index() → Muestra dashboard
 * 
 * ESTRUCTURA DE DATOS PARA VISTA:
 * - title: "Menú Principal - Módulo de Inventario"
 * - username: Nombre del usuario desde sesión
 * 
 * EJEMPLOS DE USO:
 * 
 * ```php
 * // URL principal post-login
 * // http://localhost/sistema/public/index.php?url=home/index
 * 
 * // Redirección automática desde:
 * // - Login exitoso
 * // - Acceso a raíz con sesión activa
 * ```
 */

class HomeController extends Controller {
    
    /**
     * Constructor - Exige autenticación para todas las acciones
     * 
     * DESCRIPCIÓN:
     * Aplica middleware de autenticación a todo el controlador.
     * Cualquier acceso a HomeController requiere sesión activa.
     * 
     * FLUJO:
     * 1. Llama a parent::__construct() (Controller base)
     * 2. Ejecuta $this->requireAuth()
     * 3. Si no autenticado → Redirige a /auth/login
     * 4. Si autenticado → Continúa ejecución normal
     * 
     * @access public
     */
    public function __construct() {
        $this->requireAuth();
    }
    
    /**
     * Muestra la página principal con el menú del sistema
     * 
     * DESCRIPCIÓN:
     * Carga la vista principal que sirve como dashboard y menú de navegación
     * para el sistema de inventario. Incluye el nombre del usuario actual.
     * 
     * PREPARACIÓN DE DATOS:
     * - title: Título de la página
     * - username: Nombre del usuario desde la sesión
     * 
     * VISTA ASOCIADA:
     * - home/index.php (dashboard principal)
     * 
     * EJEMPLO DE DATOS ENVIADOS A VISTA:
     * ```php
     * $data = [
     *     'title' => 'Menú Principal - Módulo de Inventario',
     *     'username' => 'teejosh'
     * ];
     * ```
     * 
     * EN LA VISTA home/index.php:
     * ```php
     * <h1><?= $title ?></h1>
     * <p>Bienvenido, <?= $username ?></p>
     * <!-- Menú de navegación -->
     * ```
     * 
     * @access public
     */
    public function index() {
        $data = [
            'title' => 'Menú Principal - Módulo de Inventario',
            'username' => $_SESSION['username'] ?? 'Usuario'
        ];
        
        $this->view('home/index', $data);
    }
}