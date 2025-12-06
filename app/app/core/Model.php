<?php
/**
 * ============================================================================
 * CLASE BASE PARA MODELOS DE DATOS
 * ============================================================================
 * 
 * ARCHIVO: Model.php
 * UBICACIÓN: /app/core/Model.php
 * 
 * DESCRIPCIÓN:
 * Clase abstracta base que todos los modelos del sistema heredan.
 * Proporciona funcionalidad común para acceso a base de datos mediante
 * el patrón Active Record simplificado.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Conexión automática a base de datos mediante Singleton
 * - Métodos protegidos para ejecutar consultas SQL
 * - Recuperación de resultados (fetchAll, fetchOne)
 * - Escape seguro de strings y manejo de errores
 * 
 * DATOS IMPORTANTES:
 * - 🟡 COMPLEJIDAD: Media
 * - 📦 HERENCIA: Todos los modelos heredan de esta clase
 * - 🔗 PATRÓN: Active Record simplificado
 * - 🗄️  BASE DE DATOS: PostgreSQL via clase Database
 * 
 * RELACIONES:
 * - HEREDADA POR: User.php, Inventario.php, y futuros modelos
 * - USA: Database.php (patrón Singleton)
 * - CARGA: Automáticamente via autoloader en config.php
 * 
 * EJEMPLO DE USO EN MODELO HIJO:
 * ```php
 * class User extends Model {
 *     public function getByEmail($email) {
 *         $result = $this->query(
 *             "SELECT * FROM usuarios WHERE email = $1",
 *             [$email]
 *         );
 *         return $this->fetchOne($result);
 *     }
 * }
 * 
 * // Uso en controlador:
 * $userModel = new User();
 * $usuario = $userModel->getByEmail('teejosh@example.com');
 * ```
 * 
 * FLUJO DE CONEXIÓN:
 * 1. new User() → llama a Model::__construct()
 * 2. Model::__construct() → Database::getInstance()
 * 3. Database::getInstance() → Conexión Singleton a PostgreSQL
 * 4. $this->db → Referencia a instancia de Database
 * 5. $this->connection → Recurso de conexión directa
 * 
 * NOTAS DE SEGURIDAD:
 * ✅ Todos los métodos son protected (solo accesibles desde modelos hijos)
 * ✅ Herencia de consultas parametrizadas anti SQL Injection
 * ✅ Escape seguro de strings disponible
 */

class Model {
    /**
     * Instancia de Database (Singleton)
     * @var Database $db Instancia compartida de base de datos
     * @access protected
     */
    protected $db;
    
    /**
     * Recurso de conexión directa a PostgreSQL
     * @var resource $connection Conexión para uso con funciones pg_*
     * @access protected
     */
    protected $connection;
    
    /**
     * Constructor - Inicializa conexión a base de datos
     * 
     * DESCRIPCIÓN:
     * Obtiene la instancia Singleton de Database y almacena tanto la instancia
     * como el recurso de conexión directo para uso en el modelo.
     * 
     * FLUJO:
     * 1. Database::getInstance() → Retorna instancia única
     * 2. $this->db → Referencia completa a Database
     * 3. $this->connection → Recurso para funciones nativas pg_*
     * 
     * @access public
     */
    public function __construct() {
        $this->db = Database::getInstance();
        $this->connection = $this->db->getConnection();
    }
    
    /**
     * Ejecuta consulta SQL con parámetros opcionales
     * 
     * DESCRIPCIÓN:
     * Método wrapper para Database::query() que ejecuta consultas SQL
     * de forma segura usando parámetros preparados.
     * 
     * PARÁMETROS:
     * @param string $sql Consulta SQL con placeholders $1, $2, etc.
     * @param array $params Array de parámetros para los placeholders
     * 
     * RETORNA:
     * @return resource|false Recurso de resultado PostgreSQL o false en error
     * 
     * EJEMPLOS:
     * ```php
     * // Sin parámetros
     * $result = $this->query("SELECT * FROM productos");
     * 
     * // Con parámetros (seguro contra SQL Injection)
     * $result = $this->query(
     *     "SELECT * FROM productos WHERE precio > $1 AND stock > $2",
     *     [50.00, 10]
     * );
     * ```
     * 
     * @access protected
     */
    protected function query($sql, $params = []) {
        return $this->db->query($sql, $params);
    }
    
    /**
     * Obtiene todas las filas de un resultado como array asociativo
     * 
     * DESCRIPCIÓN:
     * Convierte un recurso de resultado PostgreSQL en un array de arrays
     * asociativos, donde cada elemento representa una fila de la consulta.
     * 
     * PARÁMETROS:
     * @param resource|false $result Resultado de this->query()
     * 
     * RETORNA:
     * @return array Array de arrays asociativos, vacío si no hay resultados
     * 
     * EJEMPLO:
     * ```php
     * $result = $this->query("SELECT id, nombre FROM productos");
     * $productos = $this->fetchAll($result);
     * 
     * // $productos = [
     * //     ['id' => 1, 'nombre' => 'Carta Magic'],
     * //     ['id' => 2, 'nombre' => 'Sobre Pokemon']
     * // ]
     * ```
     * 
     * @access protected
     */
    protected function fetchAll($result) {
        return $this->db->fetchAll($result);
    }
    
    /**
     * Obtiene una sola fila de un resultado
     * 
     * DESCRIPCIÓN:
     * Retorna la primera fila de un resultado como array asociativo.
     * Ideal para consultas que deben retornar un solo registro.
     * 
     * PARÁMETROS:
     * @param resource|false $result Resultado de this->query()
     * 
     * RETORNA:
     * @return array|null Array asociativo con la fila o null si no hay resultados
     * 
     * EJEMPLO:
     * ```php
     * $result = $this->query("SELECT * FROM usuarios WHERE id = $1", [5]);
     * $usuario = $this->fetchOne($result);
     * 
     * // $usuario = ['id' => 5, 'nombre' => 'Juan', 'email' => 'juan@example.com']
     * ```
     * 
     * @access protected
     */
    protected function fetchOne($result) {
        return $this->db->fetchOne($result);
    }
    
    /**
     * Escapa un string para uso seguro en consultas SQL
     * 
     * DESCRIPCIÓN:
     * Escapa caracteres especiales en un string para prevenir SQL Injection.
     * ⚠️ NOTA: Preferir consultas parametrizadas sobre este método.
     * 
     * PARÁMETROS:
     * @param string $string Texto a escapar
     * 
     * RETORNA:
     * @return string String escapado seguro para consultas
     * 
     * EJEMPLO:
     * ```php
     * $nombre = "O'Reilly";
     * $nombreSeguro = $this->escape($nombre);
     * // Resultado: "O\'Reilly"
     * 
     * // MEJOR USAR PARÁMETROS:
     * $this->query("INSERT INTO productos (nombre) VALUES ($1)", [$nombre]);
     * ```
     * 
     * @access protected
     */
    protected function escape($string) {
        return $this->db->escape($string);
    }
    
    /**
     * Obtiene el último error de base de datos
     * 
     * DESCRIPCIÓN:
     * Retorna el mensaje del último error ocurrido en la base de datos.
     * Útil para debugging y manejo de errores en los modelos.
     * 
     * RETORNA:
     * @return string Mensaje de error o indicación de no conexión
     * 
     * EJEMPLO:
     * ```php
     * $result = $this->query("SELECT * FROM tabla_inexistente");
     * if (!$result) {
     *     error_log("Error BD: " . $this->getLastError());
     * }
     * ```
     * 
     * @access protected
     */
    protected function getLastError() {
        return $this->db->getLastError();
    }
}