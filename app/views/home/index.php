<?php
/**
 * ============================================================================
 * VISTA PRINCIPAL DEL DASHBOARD - PÁGINA DE INICIO
 * ============================================================================
 * 
 * ARCHIVO: index.php
 * UBICACIÓN: /app/views/home/index.php
 * 
 * DESCRIPCIÓN:
 * Vista principal del dashboard del sistema Teejosh que se muestra después del login exitoso.
 * Proporciona un menú centralizado con acceso rápido a todas las operaciones de inventario.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Pantalla de bienvenida personalizada con nombre de usuario
 * - Menú de navegación con enlaces a todas las funciones del sistema
 * - Integración con layout común (header, navbar, footer)
 * - Display dinámico de título y datos de usuario
 * 
 * DATOS IMPORTANTES:
 * - 🟢 COMPLEJIDAD: Baja
 * - 🔐 SEGURIDAD: Requiere autenticación previa
 * - 🎯 RESPONSABILIDAD: Punto de entrada principal post-login
 * - 📱 DISEÑO: Interfaz de menú centralizado
 * 
 * RELACIONES:
 * - USADO POR: HomeController::index()
 * - UTILIZA: layouts/header.php, layouts/navbar.php, layouts/footer.php
 * - INTERACTÚA CON: Datos de sesión ($username), helper url()
 * - ENLACES A: Todas las operaciones de inventario (CRUD completo)
 * 
 * EJEMPLOS DE USO:
 * ```php
 * // En HomeController:
 * public function index() {
 *     $data = [
 *         'title' => 'Sistema de Inventario Teejosh',
 *         'username' => $_SESSION['username']
 *     ];
 *     $this->view('home/index', $data);
 * }
 * ```
 * 
 * FLUJO DE EJECUCIÓN:
 * 1. Usuario autenticado accede a la raíz del sistema
 * 2. HomeController carga esta vista con datos de usuario
 * 3. Se renderizan los layouts comunes (header, navbar)
 * 4. Se muestra el mensaje de bienvenida personalizado
 * 5. Se presenta el menú de operaciones de inventario
 * 6. Se cierra con el footer común
 * 
 * NOTAS CSS/HTML:
 * - Estructura: Layout completo con header → navbar → contenido → footer
 * - Contenedor principal: Menú centrado con clase "menu"
 * - Enlaces: Estilo de hipervínculos básicos, requieren estilos CSS externos
 * - Variables PHP: $title (título de la página), $username (nombre de usuario)
 * - Función url(): Helper que genera URLs absolutas para el sistema
 * - Responsive: Depende de los layouts externos y styles.css
 */

require_once VIEW_PATH . '/layouts/header.php';
require_once VIEW_PATH . '/layouts/navbar.php';
?>

<!-- CONTENIDO PRINCIPAL DEL DASHBOARD -->
<h1>Bienvenido, <?= htmlspecialchars($username) ?></h1>

<h1><?= $title ?></h1>

<!-- MENÚ DE OPERACIONES DE INVENTARIO -->
<div class="menu">
    <!-- Enlace para crear nuevos productos en inventario -->
    <a href="<?= url('inventario/create') ?>">Insertar a inventario</a>
    
    <!-- Enlace para modificar productos existentes -->
    <a href="<?= url('inventario/modificar') ?>">Modificar inventario</a>
    
    <!-- Enlace para eliminar productos del inventario -->
    <a href="<?= url('inventario/delete') ?>">Eliminar inventario</a>
    
    <!-- Enlace para visualizar todo el inventario -->
    <a href="<?= url('inventario/index') ?>">Mostrar inventario</a>
</div>

<?php require_once VIEW_PATH . '/layouts/footer.php'; ?>