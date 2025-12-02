<?php
/**
 * ============================================================================
 * MODELO DE USUARIO - AUTENTICACIÓN Y GESTIÓN DE SESIONES
 * ============================================================================
 * 
 * ARCHIVO: User.php
 * UBICACIÓN: /app/models/User.php
 * 
 * DESCRIPCIÓN:
 * Modelo especializado en autenticación y gestión de usuarios. Actualmente
 * utiliza credenciales hardcodeadas pero está diseñado para migrar a
 * base de datos con contraseñas hasheadas.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Verificación de credenciales de usuario
 * - Gestión de sesiones PHP
 * - Validación de estado de autenticación
 * - Obtención de datos del usuario actual
 * 
 * DATOS IMPORTANTES:
 * - 🟢 COMPLEJIDAD: Baja
 * - 🔐 AUTENTICACIÓN: Hardcodeada (pendiente migrar a BD)
 * - 📦 HERENCIA: Extiende de Model.php
 * - 🎯 ALCANCE: Sistema completo de autenticación
 * 
 * RELACIONES:
 * - HEREDA DE: Model.php
 * - USADO POR: AuthController.php
 * - INTERACTÚA CON: Sesiones PHP ($_SESSION)
 * 
 * CREDENCIALES ACTUALES:
 * - Usuario: 'teejosh'
 * - Contraseña: 'esis3412'
 * - Rol: 'admin'
 * 
 * ⚠️ NOTA DE SEGURIDAD:
 * Las credenciales están hardcodeadas. En producción, migrar a:
 * - Base de datos con tabla de usuarios
 * - Contraseñas hasheadas con password_hash()
 * - Sistema de roles y permisos
 * 
 * EJEMPLOS DE USO EN CONTROLADOR:
 * ```php
 * // En AuthController
 * $user = $this->model('User');
 * 
 * // Verificar login
 * $usuario = $user->authenticate($username, $password);
 * if ($usuario) {
 *     $user->createSession($usuario);
 *     redirect('home');
 * } else {
 *     $error = "Credenciales inválidas";
 * }
 * 
 * // Verificar autenticación en cualquier controlador
 * if ($user->isAuthenticated()) {
 *     // Usuario logueado
 *     $usuarioActual = $user->getCurrentUser();
 * }
 * ```
 */

class User extends Model {
    
    /**
     * Verifica las credenciales del usuario contra las credenciales hardcodeadas
     * 
     * DESCRIPCIÓN:
     * Compara username y password con los valores predefinidos.
     * ⚠️ TEMPORAL: Pendiente migrar a base de datos con passwords hasheadas.
     * 
     * PARÁMETROS:
     * @param string $username Nombre de usuario
     * @param string $password Contraseña en texto plano
     * 
     * RETORNA:
     * @return array|false Datos del usuario si credenciales son válidas, false si no
     * 
     * ESTRUCTURA DE RETORNO:
     * ```php
     * [
     *     'id' => 1,
     *     'username' => 'teejosh',
     *     'role' => 'admin'
     * ]
     * ```
     * 
     * MEJORA FUTURA:
     * ```php
     * // Con base de datos y passwords hasheadas
     * $sql = "SELECT id, username, password_hash, role FROM usuarios WHERE username = $1";
     * $user = $this->fetchOne($this->query($sql, [$username]));
     * 
     * if ($user && password_verify($password, $user['password_hash'])) {
     *     return $user;
     * }
     * return false;
     * ```
     * 
     * @access public
     */
    public function authenticate($username, $password) {
        $validUser = 'teejosh';
        $validPass = 'esis3412';
        
        if ($username === $validUser && $password === $validPass) {
            return [
                'id' => 1,
                'username' => $username,
                'role' => 'admin'
            ];
        }
        
        return false;
    }
    
    /**
     * Crea una sesión de usuario con los datos proporcionados
     * 
     * DESCRIPCIÓN:
     * Establece las variables de sesión necesarias para mantener
     * el estado de autenticación del usuario.
     * 
     * PARÁMETROS:
     * @param array $userData Datos del usuario autenticado
     * 
     * VARIABLES DE SESIÓN CREADAS:
     * - $_SESSION['loggedin'] = true (bandera de autenticación)
     * - $_SESSION['user_id'] = ID del usuario
     * - $_SESSION['username'] = Nombre de usuario
     * - $_SESSION['role'] = Rol del usuario
     * 
     * @access public
     */
    public function createSession($userData) {
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['username'] = $userData['username'];
        $_SESSION['role'] = $userData['role'];
    }
    
    /**
     * Destruye completamente la sesión del usuario
     * 
     * DESCRIPCIÓN:
     * Limpia todas las variables de sesión y destruye la sesión PHP.
     * Efectivamente "cierra sesión" del usuario.
     * 
     * FLUJO:
     * 1. session_unset() - Limpia todas las variables de sesión
     * 2. session_destroy() - Destruye la sesión en el servidor
     * 
     * @access public
     */
    public function destroySession() {
        session_unset();
        session_destroy();
    }
    
    /**
     * Verifica si existe una sesión de usuario activa y válida
     * 
     * RETORNA:
     * @return bool true si el usuario está autenticado, false si no
     * 
     * LÓGICA:
     * - Verifica que exista la variable 'loggedin' en sesión
     * - Verifica que su valor sea exactamente true
     * - No verifica tiempo de expiración (manejado por PHP)
     * 
     * @access public
     */
    public function isAuthenticated() {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    }
    
    /**
     * Obtiene los datos del usuario actual desde la sesión
     * 
     * DESCRIPCIÓN:
     * Recupera la información del usuario almacenada en la sesión.
     * Útil para mostrar información del usuario en la interfaz.
     * 
     * RETORNA:
     * @return array|null Datos del usuario o null si no está autenticado
     * 
     * ESTRUCTURA DE RETORNO:
     * ```php
     * [
     *     'id' => 1,
     *     'username' => 'teejosh',
     *     'role' => 'admin'
     * ]
     * ```
     * 
     * EJEMPLO DE USO:
     * ```php
     * // En vista navbar.php
     * $user = new User();
     * $currentUser = $user->getCurrentUser();
     * 
     * if ($currentUser) {
     *     echo "Bienvenido, " . $currentUser['username'];
     * }
     * ```
     * 
     * @access public
     */
    public function getCurrentUser() {
        if ($this->isAuthenticated()) {
            return [
                'id' => $_SESSION['user_id'] ?? null,
                'username' => $_SESSION['username'] ?? null,
                'role' => $_SESSION['role'] ?? null
            ];
        }
        return null;
    }
}