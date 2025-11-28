<?php
/**
 * ============================================================================
 * GESTIÓN DE BASE DE DATOS - PATRÓN SINGLETON
 * ============================================================================
 * 
 * ARCHIVO: database.php
 * UBICACIÓN: /config/database.php
 * 
 * DESCRIPCIÓN:
 * Clase Database que maneja la conexión a PostgreSQL usando patrón Singleton.
 * Garantiza una única conexión por ejecución y proporciona métodos seguros
 * para consultas.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Conexión Singleton a PostgreSQL (Supabase)
 * - Ejecución de consultas parametrizadas (anti SQL Injection)
 * - Métodos para fetch de datos (fetchAll, fetchOne)
 * - Escape seguro de strings y manejo de errores
 * 
 * DATOS IMPORTANTES:
 * - 🔴 COMPLEJIDAD: Avanzada
 * - 🗄️  Proveedor: Supabase (PostgreSQL en la nube)
 * - 🔐 Schema: db_teejosh
 * - ⚠️  Credenciales hardcodeadas (mejorar con .env)
 * 
 * RELACIONES:
 * - CARGADO POR: config/config.php
 * - USADO POR: Todos los modelos (User, Inventario, etc.)
 * - EXTENSIONES: Requiere extensión pgsql de PHP
 * 
 * EJEMPLOS DE USO:
 * // Obtener instancia
 * $db = Database::getInstance();
 * 
 * // Consulta con parámetros (segura)
 * $result = $db->query(
 *     "SELECT * FROM producto WHERE precio > $1 AND stock > $2",
 *     [50, 10]
 * );
 * $productos = $db->fetchAll($result);
 * 
 * NOTAS DE SEGURIDAD:
 * ⚠️  Las credenciales están hardcodeadas - migrar a variables de entorno
 * ✅  Consultas parametrizadas previenen SQL Injection
 * 🔒 Conexión única evita ataques de race condition
 */

class Database {
    // Instancia Singleton
    private static $instance = null;
    private $connection;
    
    // Configuración de Supabase
    private $host = 'aws-1-sa-east-1.pooler.supabase.com';
    private $port = '5432';
    private $dbname = 'postgres';
    private $user = 'postgres.piearfkkossvytunnrfk';
    private $password = 'patoloco090';
    private $schema = 'db_teejosh';
    
    /**
     * Constructor privado (Singleton)
     * @access private
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Establece conexión con PostgreSQL y configura schema
     * @throws Exception Si la conexión o schema fallan
     */
    private function connect() {
        $connectionString = "host={$this->host} port={$this->port} dbname={$this->dbname} user={$this->user} password={$this->password}";
        $this->connection = pg_connect($connectionString);
        
        if (!$this->connection) {
            die("Error: No se pudo conectar a la base de datos");
        }
        
        $setSchema = pg_query($this->connection, "SET search_path TO {$this->schema}, public");
        if (!$setSchema) {
            die("Error: No se pudo establecer el schema {$this->schema}");
        }
    }
    
    /**
     * Obtiene la instancia única de Database (Singleton)
     * @return Database Instancia única de la clase
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Ejecuta consulta SQL con o sin parámetros
     * @param string $sql Consulta SQL con placeholders $1, $2, etc.
     * @param array $params Parámetros para los placeholders
     * @return resource|false Resultado de la consulta o false si falla
     */
    public function query($sql, $params = []) {
        if (empty($params)) {
            return @pg_query($this->connection, $sql);
        }
        return @pg_query_params($this->connection, $sql, $params);
    }
    
    /**
     * Obtiene todas las filas de un resultado como array asociativo
     * @param resource|false $result Resultado de una consulta
     * @return array Array de arrays asociativos con los datos
     */
    public function fetchAll($result) {
        if ($result === false || $result === null) {
            return [];
        }
        
        $rows = [];
        while ($row = pg_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    
    /**
     * Obtiene una sola fila de un resultado
     * @param resource|false $result Resultado de una consulta
     * @return array|null Array asociativo con la fila o null si no hay
     */
    public function fetchOne($result) {
        if ($result === false || $result === null) {
            return null;
        }
        return pg_fetch_assoc($result);
    }
    
    /**
     * Escapa string para uso en consultas (usar preferiblemente parámetros)
     * @param string $string String a escapar
     * @return string String escapado seguro
     */
    public function escape($string) {
        return pg_escape_string($this->connection, $string);
    }
    
    /**
     * Obtiene el último error de la base de datos
     * @return string Mensaje de error o indicación de no conexión
     */
    public function getLastError() {
        if ($this->connection) {
            return pg_last_error($this->connection);
        }
        return 'No hay conexión activa';
    }
    
    /**
     * Obtiene el recurso de conexión para uso directo
     * @return resource Conexión activa a PostgreSQL
     */
    public function getConnection() {
        return $this->connection;
    }
}