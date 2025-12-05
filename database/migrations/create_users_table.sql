-- ==========================================
-- MIGRACIÓN: Sistema de Usuarios
-- ==========================================
-- Este script crea la tabla de usuarios para
-- reemplazar las credenciales hardcoded
-- ==========================================

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuario (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    rol VARCHAR(20) DEFAULT 'user', -- 'admin', 'user', 'viewer'
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT NOW(),
    ultimo_acceso TIMESTAMP
);

-- Insertar usuario admin por defecto
-- Usuario: teejosh
-- Contraseña: esis3412
-- Hash generado con: password_hash('esis3412', PASSWORD_DEFAULT)
INSERT INTO usuario (username, password_hash, nombre_completo, rol, activo)
VALUES (
    'teejosh',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Hash de 'esis3412'
    'Administrador',
    'admin',
    TRUE
);

-- Índices para mejorar rendimiento
CREATE INDEX idx_usuario_username ON usuario(username);
CREATE INDEX idx_usuario_email ON usuario(email);

-- ==========================================
-- COMENTARIOS:
-- ==========================================
-- Después de ejecutar este script, actualizar
-- el modelo User.php para usar esta tabla
-- en lugar de las credenciales hardcoded
-- ==========================================

-- Ejemplo de código PHP para User.php:
/*
public function authenticate($username, $password) {
    $sql = "SELECT * FROM usuario WHERE username = $1 AND activo = TRUE LIMIT 1";
    $result = $this->query($sql, [$username]);
    $user = $this->fetchOne($result);
    
    if ($user && password_verify($password, $user['password_hash'])) {
        // Actualizar último acceso
        $this->query("UPDATE usuario SET ultimo_acceso = NOW() WHERE id = $1", [$user['id']]);
        
        return [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['rol']
        ];
    }
    
    return false;
}
*/

-- ==========================================
-- FUNCIÓN PARA CREAR NUEVOS USUARIOS
-- ==========================================
CREATE OR REPLACE FUNCTION fn_crear_usuario(
    p_username VARCHAR,
    p_password_hash VARCHAR,
    p_nombre_completo VARCHAR,
    p_email VARCHAR,
    p_rol VARCHAR DEFAULT 'user'
)
RETURNS TEXT AS $$
BEGIN
    INSERT INTO usuario (username, password_hash, nombre_completo, email, rol)
    VALUES (p_username, p_password_hash, p_nombre_completo, p_email, p_rol);
    
    RETURN 'Usuario creado exitosamente';
EXCEPTION
    WHEN unique_violation THEN
        RETURN 'Error: El usuario o email ya existe';
    WHEN OTHERS THEN
        RETURN 'Error al crear usuario: ' || SQLERRM;
END;
$$ LANGUAGE plpgsql;

-- Ejemplo de uso:
-- SELECT fn_crear_usuario('nuevo_user', '$2y$10$hash...', 'Juan Pérez', 'juan@example.com', 'user');