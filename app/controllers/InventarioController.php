<?php
/**
 * ============================================================================
 * CONTROLADOR DE INVENTARIO - OPERACIONES CRUD COMPLETAS
 * ============================================================================
 * 
 * ARCHIVO: InventarioController.php
 * UBICACIÓN: /app/controllers/InventarioController.php
 * 
 * DESCRIPCIÓN:
 * Controlador principal que maneja todas las operaciones del sistema de inventario.
 * Coordina entre las vistas y el modelo Inventario para proporcionar funcionalidad
 * completa de CRUD, búsqueda, modificación y operaciones especializadas.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Listado y búsqueda de items del inventario
 * - Creación de nuevos productos e items
 * - Eliminación de items del inventario
 * - Modificación de productos existentes
 * - Reabastecimiento de stock
 * - Operaciones de restock entre items
 * - Gestión de menús de operaciones
 * 
 * DATOS IMPORTANTES:
 * - 🔴 COMPLEJIDAD: Alta
 * - 🔐 SEGURIDAD: Requiere autenticación
 * - 📦 HERENCIA: Extiende de Controller.php
 * - 🎯 ALCANCE: Todo el módulo de inventario
 * - 🔗 MODELO: Inventario.php (operaciones de BD)
 * 
 * RELACIONES:
 * - HEREDA DE: Controller.php
 * - USA: Inventario.php (modelo de datos)
 * - CARGA: 7 vistas diferentes en /inventario/
 * - RUTAS: 8 métodos públicos con URLs específicas
 * 
 * ESTRUCTURA DE VISTAS:
 * - index.php → Listado principal de items
 * - create.php → Formulario de creación
 * - delete.php → Interfaz de eliminación
 * - modificar_menu.php → Menú de opciones de modificación
 * - editar.php → Formulario de edición de productos
 * - reabastecer.php → Formulario de reabastecimiento
 * - restock.php → Formulario de operación restock
 * 
 * OPERACIONES DISPONIBLES:
 * 
 * MÉTODO       | URL                  | VISTA              | DESCRIPCIÓN
 * -------------|----------------------|-------------------|------------------------------
 * index()      | /inventario          | index.php         | Listado principal
 * buscar()     | /inventario/buscar   | (redirige)        | Búsqueda de items
 * create()     | /inventario/create   | create.php        | Crear nuevo producto
 * delete()     | /inventario/delete   | delete.php        | Eliminar item
 * modificar()  | /inventario/modificar| modificar_menu.php| Menú de modificaciones
 * editar()     | /inventario/editar   | editar.php        | Editar producto
 * reabastecer()| /inventario/reabastecer| reabastecer.php | Reabastecer stock
 * restock()    | /inventario/restock  | restock.php       | Operación restock
 */

class InventarioController extends Controller {
    
    /**
     * Instancia del modelo Inventario para operaciones de datos
     * @var Inventario $inventarioModel
     * @access private
     */
    private $inventarioModel;
    
    /**
     * Constructor - Inicializa autenticación y modelo de inventario
     * 
     * DESCRIPCIÓN:
     * Aplica middleware de autenticación y carga el modelo Inventario
     * que se utilizará para todas las operaciones de este controlador.
     * 
     * FLUJO:
     * 1. Ejecuta $this->requireAuth() (redirige si no autenticado)
     * 2. Carga modelo Inventario mediante $this->model('Inventario')
     * 3. Asigna la instancia a $this->inventarioModel
     * 
     * @access public
     */
    public function __construct() {
        $this->requireAuth();
        $this->inventarioModel = $this->model('Inventario');
    }
    
    /**
     * Muestra el listado principal de todos los items del inventario
     * 
     * DESCRIPCIÓN:
     * Presenta la vista principal con todos los items del inventario.
     * Soporta mostrar resultados de búsqueda almacenados en sesión.
     * 
     * FLUJO:
     * 1. Verifica si hay resultados de búsqueda en $_SESSION['items']
     * 2. Si hay → Usa esos resultados y limpia la sesión
     * 3. Si no hay → Obtiene todos los items del modelo
     * 4. Prepara datos y carga la vista
     * 
     * DATOS PARA VISTA:
     * - title: "Items del Inventario"
     * - items: Array de items con información completa
     * 
     * VISTA ASOCIADA:
     * - inventario/index.php (tabla de items)
     * 
     * @access public
     */
    public function index() {
        // Verificar si hay resultados de búsqueda en sesión
        if (isset($_SESSION['items'])) {
            $items = $_SESSION['items'];
            unset($_SESSION['items']); // Limpiar sesión después de usar
        } else {
            // Obtener todos los items del inventario
            $items = $this->inventarioModel->getAllItems();
        }
        
        $data = [
            'title' => 'Items del Inventario',
            'items' => $items
        ];
        
        $this->view('inventario/index', $data);
    }
    
    /**
     * Procesa búsqueda de items y redirige al listado
     * 
     * DESCRIPCIÓN:
     * Maneja búsquedas por ID numérico o nombre de producto.
     * Almacena resultados en sesión y redirige al listado principal.
     * 
     * PARÁMETROS GET:
     * - query (string): Término de búsqueda (ID o texto)
     * 
     * FLUJO:
     * 1. Obtiene término de búsqueda de $_GET['query']
     * 2. Si no está vacío → Ejecuta búsqueda en modelo
     * 3. Almacena resultados en $_SESSION['items']
     * 4. Redirige a /inventario/index
     * 
     * BÚSQUEDA INTELIGENTE:
     * - Si query es numérico → Busca por ID exacto
     * - Si query es texto → Busca por nombre (LIKE case-insensitive)
     * 
     * @access public
     */
    public function buscar() {
        $query = trim($this->get('query', ''));
        
        if (!empty($query)) {
            $items = $this->inventarioModel->searchItems($query);
            $_SESSION['items'] = $items; // Almacenar en sesión para listado
        }
        
        $this->redirect('inventario/index');
    }
    
    /**
     * Maneja la creación de nuevos productos e items (GET y POST)
     * 
     * DESCRIPCIÓN:
     * Proporciona formulario para crear nuevos productos y procesa su envío.
     * Incluye datos de catálogos para dropdowns y manejo de IDs automáticos.
     * 
     * FLUJO GET (mostrar formulario):
     * 1. Prepara datos de catálogos (categorías, franquicias, etc.)
     * 2. Obtiene próximo ID disponible
     * 3. Carga vista con formulario vacío
     * 
     * FLUJO POST (procesar formulario):
     * 1. Recoge y valida datos del formulario
     * 2. Ejecuta inserción en modelo
     * 3. Muestra resultado (éxito/error)
     * 4. Actualiza próximo ID si fue exitoso
     * 
     * DATOS PARA VISTA:
     * - title: "Añadir Item"
     * - nuevo_id: Próximo ID disponible
     * - categorias, franquicias, ediciones, lenguajes: Listas para dropdowns
     * - message: Resultado de operación
     * 
     * VISTA ASOCIADA:
     * - inventario/create.php (formulario de creación)
     * 
     * @access public
     */
    public function create() {
        $data = [
            'title' => 'Añadir Item',
            'nuevo_id' => $this->inventarioModel->getNextProductoId(),
            'categorias' => $this->inventarioModel->getCategorias(),
            'franquicias' => $this->inventarioModel->getFranquicias(),
            'ediciones' => $this->inventarioModel->getEdiciones(),
            'lenguajes' => $this->inventarioModel->getLenguajes(),
            'message' => ''
        ];
        
        // Procesar formulario de creación (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->post('insertar')) {
            $insertData = [
                'id_producto' => $data['nuevo_id'],
                'nombre' => $this->post('nombre'),
                'descripcion' => $this->post('descripcion'),
                'categoria' => $this->post('categoria'),
                'franquicia' => $this->post('franquicia'),
                'edicion' => $this->post('edicion', ''), // Opcional
                'lenguaje' => $this->post('lenguaje'),
                'precio' => $this->post('precio'),
                'cantidad' => $this->post('cantidad')
            ];
            
            $result = $this->inventarioModel->insertProductoItem($insertData);
            $data['message'] = $result;
            
            // Actualizar próximo ID si la inserción fue exitosa
            if ($result['success']) {
                $data['nuevo_id'] = $this->inventarioModel->getNextProductoId();
            }
        }
        
        $this->view('inventario/create', $data);
    }
    
    /**
     * Maneja la eliminación de items del inventario (GET y POST)
     * 
     * DESCRIPCIÓN:
     * Muestra lista de items disponibles para eliminar y procesa
     * las solicitudes de eliminación.
     * 
     * FLUJO GET (mostrar interfaz):
     * 1. Obtiene todos los items para mostrar
     * 2. Prepara vista con lista y formularios de eliminación
     * 
     * FLUJO POST (procesar eliminación):
     * 1. Obtiene ID del item a eliminar
     * 2. Ejecuta eliminación en modelo
     * 3. Muestra resultado y recarga lista
     * 
     * DATOS PARA VISTA:
     * - title: "Eliminar Producto del Inventario"
     * - items: Lista de items disponibles
     * - message: Resultado de operación
     * 
     * VISTA ASOCIADA:
     * - inventario/delete.php (interfaz de eliminación)
     * 
     * @access public
     */
    public function delete() {
        $data = [
            'title' => 'Eliminar Producto del Inventario',
            'items' => $this->inventarioModel->getAllItems(),
            'message' => ''
        ];
        
        // Procesar eliminación (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->post('eliminar')) {
            $id = $this->post('id');
            $result = $this->inventarioModel->deleteItem($id);
            $data['message'] = $result;
            
            // Recargar lista de items después de eliminar
            $data['items'] = $this->inventarioModel->getAllItems();
        }
        
        $this->view('inventario/delete', $data);
    }
    
    /**
     * Muestra el menú de opciones de modificación
     * 
     * DESCRIPCIÓN:
     * Vista simple que presenta las diferentes opciones de modificación
     * disponibles en el sistema.
     * 
     * DATOS PARA VISTA:
     * - title: "Menú de Modificaciones"
     * 
     * VISTA ASOCIADA:
     * - inventario/modificar_menu.php (menú de opciones)
     * 
     * @access public
     */
    public function modificar() {
        $data = [
            'title' => 'Menú de Modificaciones'
        ];
        
        $this->view('inventario/modificar_menu', $data);
    }
    
    /**
     * Maneja la modificación de productos existentes (GET y POST)
     * 
     * DESCRIPCIÓN:
     * Proporciona interfaz para seleccionar y modificar productos.
     * Incluye selección de producto y formulario de edición.
     * 
     * FLUJO COMPLEJO:
     * 1. GET: Muestra lista de productos y formulario vacío
     * 2. POST (seleccionar producto): Carga datos del producto seleccionado
     * 3. POST (modificar): Ejecuta modificación en modelo
     * 
     * DATOS PARA VISTA:
     * - title: "Modificar Producto"
     * - productos: Lista de todos los productos
     * - categorias, franquicias: Listas para dropdowns
     * - producto: Datos del producto seleccionado (si aplica)
     * - message: Resultado de operación
     * 
     * VISTA ASOCIADA:
     * - inventario/editar.php (formulario de edición)
     * 
     * @access public
     */
    public function editar() {
        $data = [
            'title' => 'Modificar Producto',
            'productos' => $this->inventarioModel->getAllProductos(),
            'categorias' => $this->inventarioModel->getCategorias(),
            'franquicias' => $this->inventarioModel->getFranquicias(),
            'producto' => null, // Producto seleccionado
            'message' => ''
        ];
        
        // Si se seleccionó un producto para editar
        if ($this->post('producto_nombre')) {
            $producto = $this->inventarioModel->getProductoByNombre($this->post('producto_nombre'));
            $data['producto'] = $producto;
        }
        
        // Procesar modificación (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->post('modificar')) {
            $result = $this->inventarioModel->modificarProducto(
                $this->post('producto_id'),
                $this->post('nombre'),
                $this->post('descripcion'),
                $this->post('id_categoria'),
                $this->post('id_franquicia')
            );
            $data['message'] = $result;
        }
        
        $this->view('inventario/editar', $data);
    }
    
    /**
     * Maneja el reabastecimiento de stock de items (GET y POST)
     * 
     * DESCRIPCIÓN:
     * Permite aumentar el stock de items existentes mediante
     * interfaz simple de selección y cantidad.
     * 
     * FLUJO:
     * 1. GET: Muestra formulario con lista de items
     * 2. POST: Ejecuta reabastecimiento y muestra resultado
     * 
     * DATOS PARA VISTA:
     * - title: "Reabastecer Stock"
     * - items: Lista de items para dropdown
     * - message: Resultado de operación
     * 
     * VISTA ASOCIADA:
     * - inventario/reabastecer.php (formulario reabastecimiento)
     * 
     * @access public
     */
    public function reabastecer() {
        $data = [
            'title' => 'Reabastecer Stock',
            'items' => $this->inventarioModel->getItemsParaSelect(),
            'message' => ''
        ];
        
        // Procesar reabastecimiento (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->post('reabastecer')) {
            $result = $this->inventarioModel->reabastecerItem(
                $this->post('item'),
                $this->post('cantidad')
            );
            $data['message'] = $result;
            
            // Recargar lista de items (puede haber cambios en stock)
            $data['items'] = $this->inventarioModel->getItemsParaSelect();
        }
        
        $this->view('inventario/reabastecer', $data);
    }
    
    /**
     * Maneja operaciones de restock entre items (GET y POST)
     * 
     * DESCRIPCIÓN:
     * Proporciona interfaz para transferir stock entre items
     * según relación de conversión específica.
     * 
     * PARÁMETROS DE RESTOK:
     * - item_fuente: ID del item que provee unidades
     * - item_destino: ID del item que recibe unidades
     * - unidades_por_item: Relación de conversión
     * - cantidad: Cantidad de items destino a producir
     * 
     * DATOS PARA VISTA:
     * - title: "Restock de Productos"
     * - items: Lista de items para dropdowns
     * - message: Resultado de operación
     * 
     * VISTA ASOCIADA:
     * - inventario/restock.php (formulario restock)
     * 
     * @access public
     */
    public function restock() {
        $data = [
            'title' => 'Restock de Productos',
            'items' => $this->inventarioModel->getItemsParaSelect(),
            'message' => ''
        ];
        
        // Procesar operación restock (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->post('restock')) {
            $result = $this->inventarioModel->restockItem(
                $this->post('item_fuente'),
                $this->post('item_destino'),
                $this->post('unidades_por_item'),
                $this->post('cantidad')
            );
            $data['message'] = $result;
            
            // Recargar lista de items (stock habrá cambiado)
            $data['items'] = $this->inventarioModel->getItemsParaSelect();
        }
        
        $this->view('inventario/restock', $data);
    }
}