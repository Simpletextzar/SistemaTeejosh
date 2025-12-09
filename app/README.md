# 🎮 Sistema de Inventario Teejosh

Sistema de gestión de inventario desarrollado con arquitectura MVC pura para tiendas especializadas en productos coleccionables (TCG, figuras, juguetes y autos). Construido con PHP nativo, PostgreSQL y CSS vanilla, sin dependencias externas.

![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat&logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-14%2B-316192?style=flat&logo=postgresql)
![License](https://img.shields.io/badge/license-MIT-green)

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Requisitos Previos](#-requisitos-previos)
- [Instalación](#-instalación)
- [Configuración](#-configuración)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Base de Datos](#-base-de-datos)
- [Uso del Sistema](#-uso-del-sistema)
- [Rutas y Endpoints](#-rutas-y-endpoints)
- [Arquitectura MVC](#-arquitectura-mvc)
- [Guía de Desarrollo](#-guía-de-desarrollo)
- [Integración con Electron](#-integración-con-electron)
- [Desarrollo Futuro](#-desarrollo-futuro)

## ✨ Características

### Sistema de Autenticación
- 🔐 Login con sesiones PHP seguras
- 🔒 Middleware de autenticación en controladores
- 🚪 Redirección automática basada en estado de sesión
- 👤 Usuario predeterminado: `teejosh` / `esis3412`

### Gestión de Inventario Completa
- 📦 **CRUD completo** de productos e items
- 🔍 **Búsqueda inteligente** por nombre o ID
- 🏷️ **Sistema multinivel**: productos → items con variantes (edición, idioma)
- 📊 **Control de stock** en tiempo real
- 🔄 **Reabastecimiento** rápido de items existentes
- 🎁 **Sistema de restock** para conversión de unidades (ej: displays → sobres)

### Catálogos Dinámicos
- Categorías (TCG, Juguetes, Figuras, Autos, Juegos de Mesa)
- Franquicias (Pokémon, Magic, Yu-Gi-Oh!, One Piece, etc.)
- Ediciones (1ra, 2da, 3ra edición)
- Lenguajes (Inglés, Español, Francés)

### Características Técnicas
- 🏗️ **Arquitectura MVC pura** sin frameworks
- 🐘 **PostgreSQL** con funciones almacenadas
- 🎨 **CSS vanilla** con diseño grid moderno
- 🔧 **Autoloader PSR-4** simplificado
- 🛡️ **Consultas parametrizadas** (anti SQL Injection)
- 📝 **Código completamente documentado**
- 🖥️ **Compatible con Electron** para aplicación de escritorio

## 🔧 Requisitos Previos

### Requisitos del Sistema

- **PHP 8.0+** con las siguientes extensiones:
  - `pgsql` (PostgreSQL driver)
  - `pdo_pgsql` (PDO para PostgreSQL)
  - `session` (manejo de sesiones)
- **PostgreSQL 14+**
- **Servidor web** (Apache, Nginx o PHP built-in server)

### Verificar instalación

```bash
# Verificar versión de PHP
php -v

# Verificar extensiones PostgreSQL
php -m | grep pgsql

# Verificar PostgreSQL
psql --version
```

## 📥 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/teejosh-inventory.git
cd teejosh-inventory
```

### 2. Configurar PostgreSQL

```bash
# Acceder a PostgreSQL
psql -U postgres

# Crear la base de datos
CREATE DATABASE teejosh;

# Conectarse a la base de datos
\c teejosh

# Importar el esquema completo
\i database/migrations/dump.sql
```

### 3. Configurar conexión a base de datos

Edita `config/database.php` con tus credenciales:

```php
private $host = '127.0.0.1';        // Host de PostgreSQL
private $port = '5432';             // Puerto (por defecto 5432)
private $dbname = 'teejosh';        // Nombre de tu base de datos
private $user = 'postgres';         // Usuario de PostgreSQL
private $password = 'tu_password';  // Tu contraseña
private $schema = 'public';         // Schema a utilizar
```

### 4. Configurar URL base (si es necesario)

Si instalas en un subdirectorio, ajusta en `config/config.php`:

```php
// Para instalación en subdirectorio
define('BASE_URL', '/teejosh/public/index.php?url=');

// Para instalación en raíz
define('BASE_URL', 'index.php?url=');
```

### 5. Iniciar el servidor

**Opción A: PHP Built-in Server (Desarrollo)**
```bash
cd public
php -S localhost:8000
```

**Opción B: Apache**
```apache
<VirtualHost *:80>
    ServerName teejosh.local
    DocumentRoot /ruta/al/proyecto/public
    
    <Directory /ruta/al/proyecto/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 6. Acceder a la aplicación

Abre tu navegador en:
```
http://localhost:8000
```

**Credenciales por defecto:**
- Usuario: `teejosh`
- Contraseña: `esis3412`

> ⚠️ **IMPORTANTE:** Cambia estas credenciales antes de usar en producción

## ⚙️ Configuración

### Archivo config/config.php

```php
// Zona horaria
date_default_timezone_set('America/Lima');

// Modo debug (DESACTIVAR en producción)
define('DEBUG_MODE', true);

// URL base para Electron
define('BASE_URL', 'index.php?url=');

// Rutas del sistema (generadas automáticamente)
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('VIEW_PATH', APP_PATH . '/views');
```

### Configuración para producción

1. **Desactivar modo debug:**
   ```php
   define('DEBUG_MODE', false);
   ```

2. **Asegurar archivos sensibles:**
   ```bash
   chmod 640 config/database.php
   chmod 640 config/config.php
   ```

3. **Configurar logs de errores:**
   ```php
   ini_set('log_errors', 1);
   ini_set('error_log', '/ruta/personalizada/error.log');
   ```

## 📁 Estructura del Proyecto

```
app/
│
├── 📁 app/
│   ├── 📁 controllers/                 # Controladores MVC
│   │   ├── AuthController.php           # Autenticación (login/logout)
│   │   ├── HomeController.php           # Dashboard principal
│   │   └── InventarioController.php     # Gestión completa de inventario
│   │
│   ├── 📁 models/                      # Modelos de datos
│   │   ├── User.php                     # Autenticación de usuarios
│   │   └── Inventario.php               # Operaciones de inventario
│   │
│   ├── 📁 views/                       # Vistas (HTML + PHP)
│   │   ├── 📁 layouts/                 # Componentes reutilizables
│   │   │   ├── header.php
│   │   │   ├── navbar.php
│   │   │   └── footer.php
│   │   │
│   │   ├── 📁 auth/                    # Login
│   │   │   └── login.php
│   │   │
│   │   ├── 📁 home/                    # Dashboard
│   │   │   └── index.php
│   │   │
│   │   └── 📁 inventario/              # Gestión de inventario
│   │       ├── index.php
│   │       ├── create.php
│   │       ├── delete.php
│   │       ├── modificar_menu.php
│   │       ├── editar.php
│   │       ├── reabastecer.php
│   │       └── restock.php
│   │
│   └── 📁 core/                        # Núcleo del framework MVC
│       ├── Controller.php              # Clase base de controladores
│       ├── Model.php                   # Clase base de modelos
│       ├── Router.php                  # Enrutador
│       └── helpers.php                 # Funciones auxiliares
│
├── 📁 config/                          # Configuración
│   ├── config.php                      # Configuración principal
│   └── database.php                    # Conexión a base de datos
│
├── 📁 public/                          # Carpeta pública (DocumentRoot)
│   ├── 📁 css/                         # Estilos CSS
│   │   ├── reset.css
│   │   └── styles.css
│   └── index.php                       # Front Controller (punto de entrada)
│
├── 📁 database/                        # Migraciones y esquemas
│   └── 📁 migrations/
│       └── create_users_table.sql
│
├── README.md
└── STRUCTURE.md
```

### Descripción de componentes clave

| Componente | Descripción | Líneas de código |
|------------|-------------|------------------|
| **InventarioController.php** | Lógica de negocio del inventario (8 métodos públicos) | ~300 |
| **Inventario.php** | 20+ métodos para operaciones de BD | ~500 |
| **Router.php** | Sistema de enrutamiento con detección automática | ~100 |
| **Database.php** | Conexión Singleton con métodos seguros | ~150 |
| **Controller.php** | 15+ métodos auxiliares para controladores | ~200 |

## 🗄️ Base de Datos

### Esquema principal (PostgreSQL)

#### Tablas de Catálogo (Datos maestros)
```sql
-- Categorías de productos
categoria (id, nombre)
  └─ Valores: 'Cartas TCG', 'Juguetes', 'Figuras', 'Autos', 'Juegos de Mesa'

-- Franquicias/Marcas
franquicia (id, nombre)
  └─ Valores: 'Pokémon', 'Magic', 'One Piece', 'Yu-Gi-Oh!', 'Hot Wheels', etc.

-- Ediciones de productos
edicion (id, nombre)
  └─ Valores: '1ra Edición', '2da Edición', '3ra Edición'

-- Idiomas disponibles
lenguaje (id, nombre)
  └─ Valores: 'Inglés', 'Español', 'Francés'
```

#### Tablas Principales
```sql
-- Productos (información general)
producto (
  id INT PRIMARY KEY,
  nombre VARCHAR(60),
  descripcion TEXT,
  id_categoria INT FK,
  id_franquicia INT FK
)

-- Items (variantes específicas con stock)
item (
  id INT PRIMARY KEY,
  id_producto INT FK,      -- Relación con producto
  precio NUMERIC(6,2),
  cantidad INT,            -- Stock actual
  id_edicion INT FK NULL,  -- Opcional
  id_lenguaje INT FK,
  fecha_ingreso TIMESTAMP
)
```

### Modelo de relaciones

```
producto (1) ───→ (N) item
    │                  │
    ├─→ categoria      ├─→ edicion (opcional)
    └─→ franquicia     └─→ lenguaje
```

### Funciones Almacenadas (PostgreSQL)

El sistema incluye 3 funciones almacenadas para operaciones complejas:

#### 1. fn_modificar_producto()
```sql
-- Actualiza información de un producto
SELECT fn_modificar_producto(
  1,                    -- ID del producto
  'Nuevo nombre',       -- Nombre
  'Nueva descripción',  -- Descripción
  2,                    -- ID categoría
  3                     -- ID franquicia
);
```

#### 2. fn_reabastecer_item()
```sql
-- Agrega stock a un item existente
SELECT fn_reabastecer_item(
  5,   -- ID del item
  50   -- Cantidad a agregar
);
-- Resultado: "Se añadieron 50 unidades. Stock actual: 125."
```

#### 3. fn_restock_item()
```sql
-- Convierte unidades entre items (ej: display → sobres)
SELECT fn_restock_item(
  15,  -- ID item fuente (display)
  2,   -- ID item destino (sobre)
  36,  -- Unidades por item (36 sobres por display)
  1    -- Cantidad a abrir (1 display)
);
-- Resultado: "Se han abierto 1 unidades del item fuente (ID 15), 
--             generando 36 nuevas unidades del item destino (ID 2)."
```

### Ejemplo de datos

```sql
-- Producto: Booster Box Pokémon
INSERT INTO producto VALUES (12, 'Booster Display Pokémon Escarlata y Púrpura', 
  'Caja de 36 sobres de refuerzo', 1, 1);

-- Item: Versión en español a $700
INSERT INTO item VALUES (15, 12, 700.00, 4, 1, 2, NOW());

-- Restock: Abrir 1 display para obtener 36 sobres
SELECT fn_restock_item(15, 2, 36, 1);
-- Item 15 (display): cantidad 4 → 3
-- Item 2 (sobre): cantidad 74 → 110
```

## 🚀 Uso del Sistema

### 1. Iniciar sesión

```
URL: http://localhost:8000
Usuario: teejosh
Contraseña: esis3412
```

### 2. Dashboard Principal

Después del login, accedes al menú principal con opciones:
- 📋 Mostrar inventario
- ➕ Insertar a inventario
- 🗑️ Eliminar del inventario
- ✏️ Modificar inventario

### 3. Ver inventario completo

**Ruta:** `/inventario` o click en "Mostrar inventario"

- Tabla completa con todos los items
- Columnas: ID, Nombre, Precio, Cantidad, Edición, Lenguaje, Fecha de ingreso
- Búsqueda por nombre o ID
- Botón "Mostrar todo" para resetear filtros

### 4. Crear nuevo producto

**Ruta:** `/inventario/create`

**Formulario con grid de 4 columnas:**
1. Nombre del producto
2. Descripción
3. Categoría (con autocompletado)
4. Precio
5. Franquicia (con autocompletado)
6. Cantidad inicial
7. Edición (opcional)
8. Lenguaje

- El sistema genera automáticamente el ID del producto
- Autocompletado inteligente en campos de catálogo
- Validación HTML5 en todos los campos

### 5. Eliminar producto

**Ruta:** `/inventario/delete`

- Lista de todos los items disponibles
- Selección simple por formulario
- Confirmación antes de eliminar

### 6. Modificar producto

**Ruta:** `/inventario/modificar` → `/inventario/editar`

**Flujo:**
1. Selecciona el producto a modificar
2. Formulario se carga con datos actuales
3. Edita los campos necesarios
4. Guarda cambios

### 7. Reabastecer stock

**Ruta:** `/inventario/reabastecer`

**Uso:**
- Selecciona el item del dropdown
- Ingresa cantidad a agregar
- El sistema suma automáticamente al stock actual

```
Ejemplo:
Item: Sobre Pokémon (Stock actual: 74)
Cantidad a agregar: 50
Resultado: Stock nuevo: 124
```

### 8. Restock (Conversión de unidades)

**Ruta:** `/inventario/restock`

**Uso típico: Abrir displays para obtener sobres**

```
Item fuente: Display Pokémon (ID: 15, Stock: 4)
Item destino: Sobre Pokémon (ID: 2, Stock: 74)
Unidades por item: 36 (un display contiene 36 sobres)
Cantidad a abrir: 1

Resultado:
- Display: 4 → 3 unidades
- Sobres: 74 → 110 unidades
```

## 🛣️ Rutas y Endpoints

### Sistema de URL

El sistema usa el patrón Front Controller con query strings:

```
http://localhost:8000/index.php?url=controlador/metodo/parametros
```

### Tabla de Rutas

| URL | Controlador | Método | Descripción |
|-----|-------------|--------|-------------|
| `/` o `?url=` | AuthController | login() | Página de login (si no autenticado) |
| `/` o `?url=` | HomeController | index() | Dashboard (si autenticado) |
| `?url=auth/login` | AuthController | login() | Formulario de login |
| `?url=auth/logout` | AuthController | logout() | Cerrar sesión |
| `?url=home` | HomeController | index() | Dashboard principal |
| `?url=inventario` | InventarioController | index() | Listado de inventario |
| `?url=inventario/buscar` | InventarioController | buscar() | Búsqueda de items |
| `?url=inventario/create` | InventarioController | create() | Crear producto |
| `?url=inventario/delete` | InventarioController | delete() | Eliminar item |
| `?url=inventario/modificar` | InventarioController | modificar() | Menú de modificaciones |
| `?url=inventario/editar` | InventarioController | editar() | Editar producto |
| `?url=inventario/reabastecer` | InventarioController | reabastecer() | Reabastecer stock |
| `?url=inventario/restock` | InventarioController | restock() | Conversión de unidades |

### Funciones Helper para URLs

```php
// En vistas y controladores
url('inventario/create')  // → index.php?url=inventario/create
asset('/css/styles.css')  // → /public/css/styles.css
redirect('home')          // Redirección HTTP
```

## 🏗️ Arquitectura MVC

### Flujo de una petición completa

```
1. Usuario → http://localhost:8000/index.php?url=inventario/editar/5

2. public/index.php (Front Controller)
   ├─ Carga config/config.php
   ├─ Carga config/database.php
   └─ Instancia Router

3. Router->__construct()
   ├─ parseUrl() → ['inventario', 'editar', '5']
   ├─ Detecta: InventarioController
   ├─ Detecta método: editar
   └─ Parámetros: [5]

4. new InventarioController()
   ├─ __construct() → requireAuth()
   └─ Carga modelo Inventario

5. InventarioController->editar(5)
   ├─ $this->model('Inventario')->getProductoById(5)
   ├─ Prepara $data con producto
   └─ $this->view('inventario/editar', $data)

6. Vista: app/views/inventario/editar.php
   ├─ Incluye layouts/header.php
   ├─ Incluye layouts/navbar.php
   ├─ Renderiza formulario con datos
   └─ Incluye layouts/footer.php

7. HTML → Usuario
```

### Componentes del Core

#### Router (app/core/Router.php)

**Responsabilidad:** Parsear URLs y ejecutar controladores

```php
// URL: /index.php?url=inventario/editar/25
// Parseo:
$url = ['inventario', 'editar', '25']

// Detección:
$controller = 'InventarioController'
$method = 'editar'
$params = [25]

// Ejecución:
$controller->editar(25)
```

**Características:**
- Redirección automática basada en autenticación
- Controlador por defecto: `AuthController->login()`
- Método por defecto: `index()`
- Sanitización de URLs con `FILTER_SANITIZE_URL`

#### Controller Base (app/core/Controller.php)

**Métodos disponibles para controladores hijos:**

```php
// Cargar modelo
$inventario = $this->model('Inventario');

// Cargar vista con datos
$this->view('inventario/index', ['items' => $items]);

// Redirección
$this->redirect('home');

// Autenticación
$this->isAuthenticated()  // → true/false
$this->requireAuth()      // → redirige si no autenticado

// Datos HTTP
$nombre = $this->post('nombre', 'default');
$page = $this->get('page', 1);

// Respuestas JSON
$this->json(['success' => true, 'data' => $data]);

// Seguridad
$clean = $this->sanitize($userInput);
$token = $this->generateCsrfToken();
$this->verifyCsrfToken($token);
```

#### Model Base (app/core/Model.php)

**Métodos disponibles para modelos hijos:**

```php
// Consulta simple
$result = $this->query("SELECT * FROM productos");

// Consulta parametrizada (SEGURA)
$result = $this->query(
    "SELECT * FROM productos WHERE precio > $1 AND stock > $2",
    [50, 10]
);

// Obtener todos los resultados
$productos = $this->fetchAll($result);

// Obtener un solo registro
$producto = $this->fetchOne($result);

// Escape manual (usar preferiblemente parámetros)
$safe = $this->escape($userInput);

// Ver errores
$error = $this->getLastError();
```

### Patrón Singleton en Database

```php
// Primera llamada: crea instancia y conecta
$db1 = Database::getInstance();

// Llamadas subsecuentes: retorna misma instancia
$db2 = Database::getInstance();

// $db1 === $db2 → true (misma conexión)
```

**Beneficios:**
- Una sola conexión durante toda la ejecución
- Ahorro de recursos
- Configuración centralizada

## 📖 Guía de Desarrollo

### Crear un nuevo controlador

```php
<?php
// app/controllers/ProductosController.php

class ProductosController extends Controller {
    
    private $productoModel;
    
    public function __construct() {
        $this->requireAuth();  // Requiere autenticación
        $this->productoModel = $this->model('Producto');
    }
    
    public function index() {
        $productos = $this->productoModel->getAll();
        $this->view('productos/index', [
            'title' => 'Mis Productos',
            'productos' => $productos
        ]);
    }
    
    public function ver($id) {
        $producto = $this->productoModel->getById($id);
        $this->view('productos/detalle', ['producto' => $producto]);
    }
}
```

**Acceso:** `?url=productos` o `?url=productos/ver/5`

### Crear un nuevo modelo

```php
<?php
// app/models/Producto.php

class Producto extends Model {
    
    public function getAll() {
        $sql = "SELECT * FROM producto ORDER BY nombre";
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM producto WHERE id = $1 LIMIT 1";
        $result = $this->query($sql, [$id]);
        return $this->fetchOne($result);
    }
    
    public function create($data) {
        $sql = "INSERT INTO producto (nombre, descripcion) VALUES ($1, $2)";
        return $this->query($sql, [$data['nombre'], $data['descripcion']]);
    }
}
```

### Crear una vista

```php
<?php
// app/views/productos/index.php

require_once VIEW_PATH . '/layouts/header.php';
require_once VIEW_PATH . '/layouts/navbar.php';
?>

<div class="full-container">
    <h1><?= $title ?></h1>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($productos as $producto): ?>
        <tr>
            <td><?= $producto['id'] ?></td>
            <td><?= htmlspecialchars($producto['nombre']) ?></td>
            <td>
                <a href="<?= url('productos/ver/' . $producto['id']) ?>">Ver</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once VIEW_PATH . '/layouts/footer.php'; ?>
```

### Helpers disponibles

```php
// Generar URLs del sistema
echo url('inventario/create');
// → index.php?url=inventario/create

// Generar URLs de assets (CSS, JS, imágenes)
echo asset('/css/styles.css');
// → /public/css/styles.css

// Redirección
redirect('home');

// URL actual
$current = current_url();

// Debug (solo desarrollo)
dd($variable);  // var_dump() y die()
```

### Mejores prácticas

1. **Siempre usar consultas parametrizadas:**
   ```php
   // ❌ Inseguro
   $sql = "SELECT * FROM usuarios WHERE id = {$id}";
   
   // ✅ Seguro
   $sql = "SELECT * FROM usuarios WHERE id = $1";
   $result = $this->query($sql, [$id]);
   ```

2. **Sanitizar datos en vistas:**
   ```php
   // ❌ Vulnerable a XSS
   echo $userInput;
   
   // ✅ Seguro
   echo htmlspecialchars($userInput);
   ```

3. **Usar helpers para URLs:**
   ```php
   // ❌ URL hardcodeada
   <a href="/sistema/public/index.php?url=home">Home</a>
   
   // ✅ Dinámico
   <a href="<?= url('home') ?>">Home</a>
   ```

4. **Requerir autenticación en controladores:**
   ```php
   public function __construct() {
       $this->requireAuth();  // Primera línea
   }
   ```

## 🖥️ Integración con Electron

Este proyecto está diseñado para funcionar como backend de una aplicación Electron.

### Configuración especial para Electron

En `config/config.php`:

```php
// ⚠️ CRÍTICO PARA ELECTRON: URL base simplificada
// En Electron, PHP corre en 127.0.0.1:8000 sin subdirectorios
define('BASE_URL', 'index.php?url=');
```

### Arquitectura con Electron

```
┌─────────────────────────────────────────┐
│  Aplicación Electron (Proyecto 1)      │
│                                         │
│  ┌───────────────────────────────────┐ │
│  │ Ventana de Electron (BrowserWindow)│ │
│  │  http://127.0.0.1:8000            │ │
│  └───────────────────────────────────┘ │
│              ↓                          │
│  ┌───────────────────────────────────┐ │
│  │ PHP Server (127.0.0.1:8000)      │ │
│  │ + PostgreSQL portable            │ │
│  └───────────────────────────────────┘ │
│              ↓                          │
│  ┌───────────────────────────────────┐ │
│  │ Sistema MVC Teejosh (Proyecto 2) │ │
│  │ - Controladores                  │ │
│  │ - Modelos                        │ │
│  │ - Vistas                         │ │
│  └───────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

### Proceso de empaquetado

El proyecto Electron (hermano) se encarga de:

1. **Empaquetar PHP y PostgreSQL portables**
2. **Iniciar servicios automáticamente:**
   - PostgreSQL en puerto 5432
   - PHP Server en puerto 8000
3. **Abrir ventana de Electron** cargando `http://127.0.0.1:8000`
4. **Gestionar ciclo de vida:**
   - Inicio de servicios
   - Manejo de errores
   - Cierre limpio de servicios

Ver el README del proyecto Electron para más detalles de empaquetado.

## 🔮 Desarrollo Futuro

### En progreso

- [ ] **Migración de autenticación a base de datos**
  - Tabla `usuario` con passwords hasheadas
  - Sistema de roles (admin, usuario, viewer)
  - Funciones de gestión de usuarios

### Planeado para v2.0

- [ ] **Integración con API de TCG**
  - Pokémon TCG API
  - Scryfall (Magic: The Gathering)
  - Actualización automática de precios
  - Importación de datos