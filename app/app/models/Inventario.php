<?php
/**
 * ============================================================================
 * MODELO DE INVENTARIO - GESTIÓN DE PRODUCTOS E ITEMS
 * ============================================================================
 * 
 * ARCHIVO: Inventario.php
 * UBICACIÓN: /app/models/Inventario.php
 * 
 * DESCRIPCIÓN:
 * Modelo principal que maneja toda la lógica de negocio relacionada con
 * productos, items, categorías, y operaciones de inventario. Hereda de la
 * clase Model base y utiliza funciones almacenadas de PostgreSQL para
 * operaciones complejas.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Gestión completa de productos e items (CRUD)
 * - Búsqueda y filtrado de inventario
 * - Operaciones de reabastecimiento y restock
 * - Manejo de catálogos (categorías, franquicias, ediciones, lenguajes)
 * - Integración con funciones almacenadas de PostgreSQL
 * 
 * DATOS IMPORTANTES:
 * - 🟡 COMPLEJIDAD: Media-Alta
 * - 🗄️  ESQUEMA: db_teejosh (PostgreSQL)
 * - 🔗 HERENCIA: Extiende de Model.php
 * - 🎯 ALCANCE: Todo el sistema de inventario
 * 
 * RELACIONES:
 * - HEREDA DE: Model.php (acceso a base de datos)
 * - USADO POR: InventarioController.php
 * - TABLAS BD: producto, item, categoria, franquicia, edicion, lenguaje
 * - FUNCIONES BD: fn_modificar_producto, fn_reabastecer_item, fn_restock_item
 * 
 * ESTRUCTURA DE BASE DE DATOS:
 * 
 * producto (tabla maestra)
 * ├── id (PK)
 * ├── nombre
 * ├── descripcion
 * ├── id_categoria (FK → categoria)
 * └── id_franquicia (FK → franquicia)
 * 
 * item (tabla de existencias)
 * ├── id (PK)
 * ├── id_producto (FK → producto)
 * ├── precio
 * ├── cantidad (stock)
 * ├── id_edicion (FK → edicion)
 * ├── id_lenguaje (FK → lenguaje)
 * └── fecha_ingreso
 * 
 * EJEMPLOS DE USO EN CONTROLADOR:
 * ```php
 * // En InventarioController
 * $inventario = $this->model('Inventario');
 * 
 * // Obtener todos los items
 * $items = $inventario->getAllItems();
 * 
 * // Buscar items
 * $resultados = $inventario->searchItems('Magic');
 * 
 * // Agregar nuevo producto
 * $resultado = $inventario->insertProductoItem($datosFormulario);
 * 
 * // Reabastecer stock
 * $inventario->reabastecerItem(5, 50);
 * ```
 */

class Inventario extends Model {
    
    /**
     * Obtiene todos los productos ordenados alfabéticamente
     * 
     * DESCRIPCIÓN:
     * Consulta simple que retorna todos los productos de la base de datos
     * ordenados por nombre. No incluye información de stock.
     * 
     * SQL:
     * ```sql
     * SELECT * FROM producto ORDER BY nombre
     * ```
     * 
     * RETORNA:
     * @return array Lista de productos con todos sus campos
     * 
     * EJEMPLO DE RESULTADO:
     * ```php
     * [
     *     ['id' => 1, 'nombre' => 'Carta Magic', 'descripcion' => '...'],
     *     ['id' => 2, 'nombre' => 'Sobre Pokemon', 'descripcion' => '...'],
     *     ['id' => 3, 'nombre' => 'Deck Yu-Gi-Oh', 'descripcion' => '...']
     * ]
     * ```
     * 
     * @access public
     */
    public function getAllProductos() {
        $sql = "SELECT * FROM producto ORDER BY nombre";
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
    
    /**
     * Obtiene todos los items con información relacional completa
     * 
     * DESCRIPCIÓN:
     * Consulta compleja con JOINs que retorna items con información de
     * producto, edición, lenguaje y fecha formateada. Ordenado por ID descendente.
     * 
     * SQL:
     * ```sql
     * SELECT 
     *     item.id,
     *     producto.nombre AS nombre,
     *     item.precio,
     *     item.cantidad,
     *     COALESCE(edicion.nombre, '-') AS edicion,
     *     lenguaje.nombre AS lenguaje,
     *     to_char(item.fecha_ingreso, 'YYYY-MM-DD HH24:MI:SS') AS fecha_ingreso
     * FROM item
     * INNER JOIN producto ON item.id_producto = producto.id
     * LEFT JOIN edicion ON item.id_edicion = edicion.id
     * INNER JOIN lenguaje ON item.id_lenguaje = lenguaje.id
     * ORDER BY item.id DESC
     * ```
     * 
     * RETORNA:
     * @return array Items con información expandida para mostrar en vistas
     * 
     * EJEMPLO DE RESULTADO:
     * ```php
     * [
     *     [
     *         'id' => 15,
     *         'nombre' => 'Carta Rara Black Lotus',
     *         'precio' => 1500.00,
     *         'cantidad' => 2,
     *         'edicion' => 'Alpha',
     *         'lenguaje' => 'Inglés',
     *         'fecha_ingreso' => '2024-01-15 14:30:00'
     *     ]
     * ]
     * ```
     * 
     * @access public
     */
    public function getAllItems() {
        $sql = "
        SELECT 
            item.id,
            producto.nombre AS nombre,
            item.precio,
            item.cantidad,
            COALESCE(edicion.nombre, '-') AS edicion,
            lenguaje.nombre AS lenguaje,
            to_char(item.fecha_ingreso, 'YYYY-MM-DD HH24:MI:SS') AS fecha_ingreso
        FROM item
        INNER JOIN producto ON item.id_producto = producto.id
        LEFT JOIN edicion ON item.id_edicion = edicion.id
        INNER JOIN lenguaje ON item.id_lenguaje = lenguaje.id
        ORDER BY item.id DESC
        ";
        
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
    
    /**
     * Busca items por ID numérico o nombre de producto (búsqueda parcial)
     * 
     * DESCRIPCIÓN:
     * Búsqueda inteligente que detecta si el query es numérico (busca por ID)
     * o texto (busca por nombre con LIKE). Case-insensitive para texto.
     * 
     * PARÁMETROS:
     * @param string|int $query Término de búsqueda (ID numérico o texto)
     * 
     * RETORNA:
     * @return array Items que coinciden con la búsqueda
     * 
     * EJEMPLOS:
     * ```php
     * // Búsqueda por ID
     * $items = $inventario->searchItems(15);
     * // SQL: WHERE item.id = 15
     * 
     * // Búsqueda por nombre (case-insensitive)
     * $items = $inventario->searchItems('magic');
     * // SQL: WHERE LOWER(producto.nombre) LIKE LOWER('%magic%')
     * 
     * // Búsqueda parcial
     * $items = $inventario->searchItems('lotus');
     * // Encuentra: "Black Lotus", "Lotus Bloom", etc.
     * ```
     * 
     * @access public
     */
    public function searchItems($query) {
        $sql = "
        SELECT 
            item.id,
            producto.nombre AS nombre,
            item.precio,
            item.cantidad,
            COALESCE(edicion.nombre, '-') AS edicion,
            lenguaje.nombre AS lenguaje,
            to_char(item.fecha_ingreso, 'YYYY-MM-DD HH24:MI:SS') AS fecha_ingreso
        FROM item
        INNER JOIN producto ON item.id_producto = producto.id
        LEFT JOIN edicion ON item.id_edicion = edicion.id
        INNER JOIN lenguaje ON item.id_lenguaje = lenguaje.id
        ";
        
        $params = [];
        if (is_numeric($query)) {
            $sql .= " WHERE item.id = $1";
            $params[] = $query;
        } else {
            $sql .= " WHERE LOWER(producto.nombre) LIKE LOWER($1)";
            $params[] = '%' . $query . '%';
        }
        
        $result = $this->query($sql, $params);
        return $this->fetchAll($result);
    }
    
    /**
     * Obtiene un producto específico por su nombre exacto
     * 
     * PARÁMETROS:
     * @param string $nombre Nombre exacto del producto
     * 
     * RETORNA:
     * @return array|null Datos del producto o null si no existe
     * 
     * USO TÍPICO:
     * - Verificar si un producto ya existe antes de insertar
     * - Validación de duplicados en formularios
     * 
     * @access public
     */
    public function getProductoByNombre($nombre) {
        $sql = "SELECT * FROM producto WHERE nombre = $1 LIMIT 1";
        $result = $this->query($sql, [$nombre]);
        return $this->fetchOne($result);
    }
    
    /**
     * Obtiene el próximo ID disponible para nuevos productos
     * 
     * DESCRIPCIÓN:
     * Calcula el siguiente ID autoincremental consultando el máximo ID actual.
     * Si no hay productos, retorna 1.
     * 
     * RETORNA:
     * @return int Próximo ID disponible
     * 
     * EJEMPLO:
     * ```php
     * // Si máximo ID es 25
     * $siguienteId = $inventario->getNextProductoId(); // 26
     * 
     * // Si no hay productos
     * $siguienteId = $inventario->getNextProductoId(); // 1
     * ```
     * 
     * @access public
     */
    public function getNextProductoId() {
        $sql = "SELECT MAX(id) AS max_id FROM producto";
        $result = $this->query($sql);
        
        if (!$result) {
            return 1; // Si falla, empezar desde 1
        }
        
        $row = $this->fetchOne($result);
        return ($row['max_id'] ?? 0) + 1;
    }
    
    /**
     * Obtiene o crea un registro en tablas de catálogo (categoría, franquicia, etc.)
     * 
     * DESCRIPCIÓN:
     * Función utilitaria que busca un registro por nombre en una tabla de catálogo.
     * Si no existe, lo crea automáticamente. Usa auto-increment manual.
     * 
     * PARÁMETROS:
     * @param string $tabla Nombre de la tabla (categoria, franquicia, edicion, lenguaje)
     * @param string $nombre Nombre del registro a buscar/crear
     * 
     * RETORNA:
     * @return int|null ID del registro o null si hay error
     * 
     * EJEMPLOS:
     * ```php
     * // Obtener ID de categoría "Cartas" (crear si no existe)
     * $idCategoria = $inventario->getOrCreateCatalogo('categoria', 'Cartas');
     * 
     * // Obtener ID de franquicia "Magic: The Gathering"
     * $idFranquicia = $inventario->getOrCreateCatalogo('franquicia', 'Magic: The Gathering');
     * ```
     * 
     * ⚠️ NOTA DE SEGURIDAD:
     * Esta función usa escape() pero concatenación directa de $tabla.
     * Asegurar que $tabla solo contenga nombres de tablas válidos.
     * 
     * @access public
     */
    public function getOrCreateCatalogo($tabla, $nombre) {
        if (empty($nombre)) {
            return null;
        }
        
        $nombreEscaped = $this->escape($nombre);
        
        // Buscar si existe
        $sql = "SELECT id FROM {$tabla} WHERE nombre = '{$nombreEscaped}' LIMIT 1";
        $result = $this->query($sql);
        $row = $this->fetchOne($result);
        
        if ($row) {
            return (int)$row['id'];
        }
        
        // Crear nuevo
        $sqlMax = "SELECT MAX(id) AS max_id FROM {$tabla}";
        $resultMax = $this->query($sqlMax);
        $rowMax = $this->fetchOne($resultMax);
        $nuevoId = ($rowMax['max_id'] ?? 0) + 1;
        
        $sqlInsert = "INSERT INTO {$tabla} (id, nombre) VALUES ({$nuevoId}, '{$nombreEscaped}')";
        $ok = $this->query($sqlInsert);
        
        if ($ok) {
            return $nuevoId;
        }
        
        return null;
    }
    
    /**
     * Inserta un nuevo producto y su item asociado en una transacción implícita
     * 
     * DESCRIPCIÓN:
     * Operación compleja que inserta un producto y su stock (item) en la base de datos.
     * Maneja automáticamente la creación de catálogos y relaciones.
     * 
     * PARÁMETROS:
     * @param array $data Datos del formulario con claves:
     *   - id_producto (int)
     *   - nombre (string)
     *   - descripcion (string)
     *   - categoria (string)
     *   - franquicia (string)
     *   - edicion (string)
     *   - lenguaje (string)
     *   - precio (float)
     *   - cantidad (int)
     * 
     * RETORNA:
     * @return array Resultado de la operación con claves:
     *   - success (bool)
     *   - message (string)
     * 
     * FLUJO DE LA OPERACIÓN:
     * 1. Obtener/crear IDs de catálogos (categoría, franquicia, edición, lenguaje)
     * 2. Insertar producto en tabla 'producto'
     * 3. Insertar stock en tabla 'item'
     * 4. Retornar resultado
     * 
     * EJEMPLO:
     * ```php
     * $resultado = $inventario->insertProductoItem([
     *     'id_producto' => 26,
     *     'nombre' => 'Black Lotus',
     *     'descripcion' => 'Carta mítica de Magic',
     *     'categoria' => 'Cartas Sueltas',
     *     'franquicia' => 'Magic: The Gathering',
     *     'edicion' => 'Alpha',
     *     'lenguaje' => 'Inglés',
     *     'precio' => 1500.00,
     *     'cantidad' => 1
     * ]);
     * 
     * if ($resultado['success']) {
     *     echo "Producto agregado exitosamente";
     * } else {
     *     echo "Error: " . $resultado['message'];
     * }
     * ```
     * 
     * @access public
     */
    public function insertProductoItem($data) {
        // Obtener o crear los catálogos
        $idCategoria = $this->getOrCreateCatalogo('categoria', $data['categoria']);
        $idFranquicia = $this->getOrCreateCatalogo('franquicia', $data['franquicia']);
        $idEdicion = $this->getOrCreateCatalogo('edicion', $data['edicion']);
        $idLenguaje = $this->getOrCreateCatalogo('lenguaje', $data['lenguaje']);
        
        if (!$idCategoria || !$idFranquicia || !$idLenguaje) {
            return ['success' => false, 'message' => 'Error al crear catálogos'];
        }
        
        $idProducto = $data['id_producto'];
        $nombre = $this->escape($data['nombre']);
        $descripcion = $this->escape($data['descripcion']);
        $precio = (float)$data['precio'];
        $cantidad = (int)$data['cantidad'];
        
        // Insertar producto
        $sqlProducto = "
            INSERT INTO producto (id, nombre, descripcion, id_categoria, id_franquicia)
            VALUES ($idProducto, '$nombre', '$descripcion', $idCategoria, $idFranquicia)
        ";
        
        $okProducto = $this->query($sqlProducto);
        
        if (!$okProducto) {
            return ['success' => false, 'message' => 'Error al insertar producto: ' . $this->getLastError()];
        }
        
        // Insertar item
        $edicionValue = $idEdicion ? $idEdicion : 'NULL';
        $sqlItem = "
            INSERT INTO item (id_producto, precio, cantidad, id_edicion, id_lenguaje, fecha_ingreso)
            VALUES ($idProducto, $precio, $cantidad, $edicionValue, $idLenguaje, NOW())
        ";
        
        $okItem = $this->query($sqlItem);
        
        if (!$okItem) {
            return ['success' => false, 'message' => 'Error al insertar item: ' . $this->getLastError()];
        }
        
        return ['success' => true, 'message' => 'Producto e item agregados correctamente'];
    }
    
    /**
     * Elimina un item del inventario por su ID
     * 
     * PARÁMETROS:
     * @param int $id ID del item a eliminar
     * 
     * RETORNA:
     * @return array Resultado con éxito y mensaje
     * 
     * NOTA:
     * Solo elimina el item (stock), no el producto asociado.
     * El producto puede tener múltiples items (diferentes ediciones/lenguajes).
     * 
     * @access public
     */
    public function deleteItem($id) {
        $sql = "DELETE FROM item WHERE id = $1";
        $result = $this->query($sql, [$id]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Item eliminado correctamente'];
        }
        
        return ['success' => false, 'message' => 'Error al eliminar el item'];
    }
    
    /**
     * Modifica un producto existente usando función almacenada de PostgreSQL
     * 
     * DESCRIPCIÓN:
     * Utiliza la función fn_modificar_producto() de PostgreSQL para actualizar
     * un producto. Esta función puede incluir validaciones y lógica adicional.
     * 
     * PARÁMETROS:
     * @param int $id ID del producto
     * @param string $nombre Nuevo nombre
     * @param string $descripcion Nueva descripción
     * @param int $idCategoria Nuevo ID de categoría
     * @param int $idFranquicia Nuevo ID de franquicia
     * 
     * RETORNA:
     * @return array Resultado con éxito y mensaje
     * 
     * SQL DE LA FUNCIÓN:
     * ```sql
     * SELECT fn_modificar_producto(1, 'Nuevo Nombre', 'Nueva Desc', 2, 3)
     * ```
     * 
     * @access public
     */
    public function modificarProducto($id, $nombre, $descripcion, $idCategoria, $idFranquicia) {
        $nombreEsc = $this->escape($nombre);
        $descripcionEsc = $this->escape($descripcion);
        
        $sql = "SELECT fn_modificar_producto($id, '$nombreEsc', '$descripcionEsc', $idCategoria, $idFranquicia) AS mensaje";
        $result = $this->query($sql);
        
        if (!$result) {
            return ['success' => false, 'message' => 'Error: Función fn_modificar_producto no existe'];
        }
        
        $row = $this->fetchOne($result);
        
        return ['success' => true, 'message' => $row['mensaje'] ?? 'Producto modificado'];
    }
    
    /**
     * Reabastece el stock de un item usando función almacenada
     * 
     * PARÁMETROS:
     * @param int $itemId ID del item a reabastecer
     * @param int $cantidad Cantidad a agregar al stock
     * 
     * RETORNA:
     * @return array Resultado con éxito y mensaje
     * 
     * FUNCIÓN BD:
     * ```sql
     * SELECT fn_reabastecer_item(5, 50) -- Agrega 50 unidades al item 5
     * ```
     * 
     * @access public
     */
    public function reabastecerItem($itemId, $cantidad) {
        $sql = "SELECT fn_reabastecer_item($itemId, $cantidad) AS mensaje";
        $result = $this->query($sql);
        
        if (!$result) {
            return ['success' => false, 'message' => 'Error: Función fn_reabastecer_item no existe'];
        }
        
        $row = $this->fetchOne($result);
        
        return ['success' => true, 'message' => $row['mensaje'] ?? 'Item reabastecido'];
    }
    
    /**
     * Realiza operación de restock entre items usando función almacenada
     * 
     * DESCRIPCIÓN:
     * Transfiere unidades de un item fuente a un item destino según
     * una relación de unidades por item.
     * 
     * PARÁMETROS:
     * @param int $itemFuente ID del item que provee las unidades
     * @param int $itemDestino ID del item que recibe las unidades
     * @param int $unidadesPorItem Relación de conversión (ej: 10 unidades fuente = 1 unidad destino)
     * @param int $cantidad Cantidad de items destino a producir
     * 
     * RETORNA:
     * @return array Resultado con éxito y mensaje
     * 
     * EJEMPLO:
     * ```php
     * // Convertir 100 unidades del item 5 en 10 unidades del item 8
     * $inventario->restockItem(5, 8, 10, 10);
     * ```
     * 
     * @access public
     */
    public function restockItem($itemFuente, $itemDestino, $unidadesPorItem, $cantidad) {
        $sql = "SELECT fn_restock_item($itemFuente, $itemDestino, $unidadesPorItem, $cantidad) AS mensaje";
        $result = $this->query($sql);
        
        if (!$result) {
            return ['success' => false, 'message' => 'Error: Función fn_restock_item no existe'];
        }
        
        $row = $this->fetchOne($result);
        
        return ['success' => true, 'message' => $row['mensaje'] ?? 'Restock realizado'];
    }
    
    /**
     * Obtiene todas las categorías ordenadas alfabéticamente
     * 
     * RETORNA:
     * @return array Lista de categorías
     * 
     * USO TÍPICO:
     * - Llenar dropdowns en formularios
     * - Filtros en interfaces
     * 
     * @access public
     */
    public function getCategorias() {
        $sql = "SELECT * FROM categoria ORDER BY nombre ASC";
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
    
    /**
     * Obtiene todas las franquicias ordenadas alfabéticamente
     * 
     * @return array Lista de franquicias
     * @access public
     */
    public function getFranquicias() {
        $sql = "SELECT * FROM franquicia ORDER BY nombre ASC";
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
    
    /**
     * Obtiene todas las ediciones ordenadas alfabéticamente
     * 
     * @return array Lista de ediciones
     * @access public
     */
    public function getEdiciones() {
        $sql = "SELECT * FROM edicion ORDER BY nombre ASC";
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
    
    /**
     * Obtiene todos los lenguajes ordenados alfabéticamente
     * 
     * @return array Lista de lenguajes
     * @access public
     */
    public function getLenguajes() {
        $sql = "SELECT * FROM lenguaje ORDER BY nombre ASC";
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
    
    /**
     * Obtiene items simplificados para uso en elementos select (dropdowns)
     * 
     * DESCRIPCIÓN:
     * Consulta ligera que retorna ID, nombre de producto y stock actual.
     * Optimizada para llenar elementos <select> en formularios.
     * 
     * RETORNA:
     * @return array Items con formato [id, producto, cantidad]
     * 
     * EJEMPLO DE USO:
     * ```php
     * // En formulario de restock
     * <select name="item_fuente">
     * <?php foreach ($items as $item): ?>
     *     <option value="<?= $item['id'] ?>">
     *         <?= $item['producto'] ?> (Stock: <?= $item['cantidad'] ?>)
     *     </option>
     * <?php endforeach; ?>
     * </select>
     * ```
     * 
     * @access public
     */
    public function getItemsParaSelect() {
        $sql = "
        SELECT i.id, p.nombre AS producto, i.cantidad
        FROM item i
        JOIN producto p ON p.id = i.id_producto
        ORDER BY p.nombre
        ";
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
}