<?php
/**
 * CONTROLADOR API (BASELINE ARQUITECTÓNICO)
 * Ubicación: /app/controllers/ApiController.php
 * * Maneja las peticiones que empiezan con /api/
 */

require_once __DIR__ . '/../core/Controller.php'; // Asumo que existe base Controller
require_once __DIR__ . '/../models/HealthModel.php';

class ApiController extends Controller {

    /**
     * Endpoint: GET /api/health
     * Valida la arquitectura completa (Frontend -> Router -> Controller -> Model -> DB)
     */
    public function health() {
        // 1. Instanciar el modelo
        $healthModel = new HealthModel();
        
        // 2. Verificar conexión a BD
        $dbConnected = $healthModel->checkConnection();
        
        // 3. Preparar respuesta JSON
        $response = [
            'system_status' => 'OK',
            'database_connection' => $dbConnected ? 'SUCCESS' : 'FAILURE',
            'timestamp' => date('Y-m-d H:i:s'),
            'architecture_check' => 'MVC Walking Skeleton'
        ];

        // 4. Enviar cabeceras y respuesta
        // Limpiamos cualquier output previo para asegurar que sea JSON puro
        ob_clean(); 
        header('Content-Type: application/json');
        http_response_code($dbConnected ? 200 : 500);
        
        echo json_encode($response);
        exit; // Detener ejecución para no renderizar vistas HTML extra
    }
}
