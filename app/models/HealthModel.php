<?php
/**
 * MODELO DE VERIFICACIÓN DE SALUD (BASELINE)
 * Ubicación: /app/models/HealthModel.php
 */

require_once __DIR__ . '/../core/Model.php';

class HealthModel extends Model {
    
    /**
     * Verifica la conexión a la BD ejecutando una consulta trivial.
     * * @return boolean True si la conexión es exitosa, False si falla.
     */
    public function checkConnection() {
        try {
            // Usamos el método query() heredado de tu Model.php
            // SELECT 1 es el estándar para verificar vida en PostgreSQL
            $result = $this->query("SELECT 1 as status");
            
            // Usamos fetchOne() heredado para obtener el dato
            $data = $this->fetchOne($result);
            
            // Si retorna el 1, la conexión es sólida
            return ($data && $data['status'] == 1);
        } catch (Exception $e) {
            return false;
        }
    }
}
