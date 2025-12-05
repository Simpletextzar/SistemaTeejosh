# 🎮 Sistema de Inventario Teejosh

Sistema de gestión de inventario desarrollado con arquitectura MVC para tiendas especializadas en productos coleccionables (TCG, figuras, juguetes y autos). Construido con PHP puro, PostgreSQL y CSS vanilla.

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
- [Uso](#-uso)
- [Funcionalidades](#-funcionalidades)
- [Arquitectura MVC](#-arquitectura-mvc)
- [Integración con Electron](#-integración-con-electron)
- [Desarrollo Futuro](#-desarrollo-futuro)

## ✨ Características

- 🔐 **Sistema de autenticación** con sesiones PHP
- 📦 **CRUD completo** para gestión de productos e inventario
- 🏷️ **Gestión multinivel** de items con ediciones, idiomas y categorías
- 🔄 **Sistema de restock inteligente** (conversión de displays a sobres individuales)
- 📊 **Control de stock en tiempo real**
- 🎨 **Interfaz limpia** con CSS vanilla (sin frameworks)
- 🏗️ **Arquitectura MVC pura** sin dependencias externas
- 🐘 **PostgreSQL** como motor de base de datos
- 🖥️ **Compatible con Electron** para distribución como aplicación de escritorio

## 🔧 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

- **PHP 8.0+** con las siguientes extensiones:
  - `pgsql` (PostgreSQL)
  - `pdo_pgsql` (PDO para PostgreSQL)
  - `session` (manejo de sesiones)
- **PostgreSQL 14+**
- **Servidor web** (Apache, Nginx o PHP built-in server)
- **Git** (opcional, para clonar el repositorio)

### Verificar instalación de PHP y extensiones

```bash
# Verificar versión de PHP
php -v

# Verificar extensiones instaladas
php -m | grep pgsql
```

## 📥 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/teejosh-inventory.git
cd teejosh-inventory
```

### 2. Configurar la base de datos

```bash
# Acceder a PostgreSQL
psql -U postgres

# Crear la base de datos
CREATE DATABASE teejosh;

# Conectarse a la base de datos
\c teejosh

# Importar el esquema y datos iniciales
\i database/migrations/dump.sql
```

### 3. Configurar conexión a base de datos

Edita el archivo `config/database.php` con tus credenciales:

```php
private $host = '127.0.0.1';        // Host de PostgreSQL
private $port = '5432';             // Puerto (por defecto 5432)
private $dbname = 'teejosh';        // Nombre de la base de datos
private $user = 'postgres';         // Usuario de PostgreSQL
private $password = 'tu_password';  // Contraseña
private $schema = 'public';         // Schema a utilizar
```

### 4. Iniciar el servidor

**Opción A: PHP Built-in Server (Desarrollo)**
```bash
cd public
php -S localhost:8000
```

**Opción B: Apache/Nginx**
- Configurar el DocumentRoot hacia la carpeta `public/`
- Asegurarse de tener mod_rewrite habilitado (Apache)

### 5. Acceder a la aplicación

Abre tu navegador y visita:
```
http://localhost:8000
```

**Credenciales por defecto:**
- Usuario: `teejosh`
- Contraseña: `esis3412`

> ⚠️ **IMPORTANTE:** Cambia estas credenciales en producción

## ⚙️ Configuración

### Variables de entorno importantes

En `config/config.php` puedes ajustar:

```php
// Zona horaria
date_default_timezone_set('America/Lima');

// Modo debug (desactivar en producción)
define('DEBUG_MODE', false);

// URL base (importante para Electron)
define('BASE_URL', 'index.php?url=');
```

### Configuración para producción

1. **Desactivar modo debug:**
   ```php
   define('DEBUG_MODE', false);
   ```

2. **Configurar credenciales seguras:**
   - Cambia las credenciales de la base de datos
   - Actualiza el usuario y contraseña de autenticación

3. **Configurar permisos:**
   ```bash
   chmod 755 public/
   chmod 644 public/index.php
   ```

## 📁 Estructura del Proyecto

```
app/
│
├── 📁 app/
│   ├── 📁 controllers/                 # Controladores MVC
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   └── InventarioController.php
│   │
│   ├── 📁 models/                      # Modelos de datos
│   │   ├── User.php
│   │   └── Inventario.php
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
│   └── index.php                       # Punto de entrada
│
├── 📁 database/                        # Migraciones y esquemas
│   └── 📁 migrations/
│       └── create_users_table.sql
│
├── README.md
└── STRUCTURE.md
```

### Descripción de carpetas clave

| Carpeta | Descripción |
|---------|-------------|
| `app/controllers/` | Lógica de negocio y manejo de peticiones |
| `app/models/` | Interacción con la base de datos |
| `app/views/` | Plantillas HTML para la interfaz de usuario |
| `app/core/` | Framework MVC personalizado |
| `config/` | Archivos de configuración del sistema |
| `public/` | Archivos accesibles públicamente (CSS, JS, imágenes) |

## 🗄️ Base de Datos

### Esquema principal

El sistema utiliza las siguientes tablas:

#### Tablas de Catálogo
- **categoria** - Categorías de productos (TCG, Juguetes, Figuras, Autos, Juegos de Mesa)
- **franquicia** - Franquicias (Pokémon, Magic, One Piece, Yu-Gi-Oh!, etc.)
- **edicion** - Ediciones de productos (1ra, 2da, 3ra edición)
- **lenguaje** - Idiomas disponibles (Inglés, Español, Francés)

#### Tablas Principales
- **producto** - Información general del producto
- **item** - Variantes específicas de productos (con precio, stock, edición, idioma)
- **cliente** - Información de clientes
- **reg_venta** - Registro de ventas
- **producto_venta** - Detalle de productos vendidos

### Modelo de datos simplificado

```
producto (1) ──→ (N) item
    │                │
    ├─→ categoria    ├─→ edicion
    └─→ franquicia   └─→ lenguaje
```

### Funciones de PostgreSQL

El sistema incluye funciones almacenadas para operaciones complejas:

- `fn_modificar_producto()` - Actualizar datos de productos
- `fn_reabastecer_item()` - Agregar stock a un item existente
- `fn_restock_item()` - Convertir displays en sobres individuales

### Ejemplo de uso de funciones

```sql
-- Reabastecer un item (agregar 50 unidades al item con ID 5)
SELECT fn_reabastecer_item(5, 50);

-- Restock: abrir 1 display (ID 15) que contiene 36 sobres (ID 2)
SELECT fn_restock_item(15, 2, 36, 1);
```

## 🚀 Uso

### Iniciar sesión

1. Accede a `http://localhost:8000`
2. Ingresa las credenciales:
   - Usuario: `teejosh`
   - Contraseña: `esis3412`

### Gestión de inventario

#### Ver inventario completo
```
http://localhost:8000/index.php?url=inventario
```

#### Crear nuevo producto
1. Click en "Agregar Producto"
2. Completa el formulario:
   - Nombre del producto
   - Descripción
   - Categoría
   - Franquicia
   - Precio
   - Stock inicial
   - Edición (opcional)
   - Idioma

#### Editar producto existente
1. En la lista de inventario, click en "Editar"
2. Modifica los campos necesarios
3. Guarda los cambios

#### Reabastecer stock
1. Click en "Reabastecer" en el producto deseado
2. Ingresa la cantidad a agregar
3. Confirma la operación

#### Restock (Displays → Sobres)
1. Click en "Restock"
2. Selecciona el item fuente (display)
3. Selecciona el item destino (sobre individual)
4. Ingresa la cantidad de displays a abrir
5. Define unidades por display (ej: 36 sobres)
6. Confirma la operación

## 🎯 Funcionalidades

### Módulo de Autenticación
- ✅ Login con validación de credenciales
- ✅ Gestión de sesiones PHP
- ✅ Protección de rutas (middleware)
- ✅ Cierre de sesión

### Módulo de Inventario
- ✅ **CRUD completo:**
  - Crear productos con múltiples variantes
  - Listar inventario con filtros
  - Editar información de productos
  - Eliminar productos (con validación)
  
- ✅ **Gestión de stock:**
  - Reabastecimiento simple
  - Sistema de restock (conversión de unidades)
  - Alertas de stock bajo
  - Historial de movimientos

- ✅ **Organización multinivel:**
  - Productos por categoría
  - Filtrado por franquicia
  - Variantes por edición e idioma
  - Precios diferenciados por variante

### Características técnicas
- 🔒 **Seguridad:** Consultas parametrizadas (prevención de SQL Injection)
- 🎨 **Interfaz:** Diseño responsive con CSS vanilla
- ⚡ **Rendimiento:** Patrón Singleton para conexiones DB
- 🧩 **Modularidad:** Arquitectura MVC desacoplada
- 📝 **Mantenibilidad:** Código documentado y estructurado

## 🏗️ Arquitectura MVC

### Flujo de una petición

```
Usuario → public/index.php → Router → Controller → Model → Database
                                           ↓
Usuario ← View ← Controller ←──────────────┘
```

### Componentes del Core

#### Router (`app/core/Router.php`)
Maneja el enrutamiento de URLs a controladores:
```php
// URL: /inventario/editar/5
// Se mapea a: InventarioController->editar(5)
```

#### Controller (`app/core/Controller.php`)
Clase base para todos los controladores:
```php
class InventarioController extends Controller {
    public function index() {
        // Lógica de negocio
        $this->view('inventario/index', $data);
    }
}
```

#### Model (`app/core/Model.php`)
Clase base para modelos con acceso a la base de datos:
```php
class Inventario extends Model {
    public function getAll() {
        $sql = "SELECT * FROM item";
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
}
```

## 🖥️ Integración con Electron

Este proyecto está diseñado para ser empaquetado en una aplicación de escritorio usando Electron.

### Configuración especial para Electron

En `config/config.php`:
```php
// URL base simplificada para Electron
// PHP corre en 127.0.0.1:8000 sin subdirectorios
define('BASE_URL', 'index.php?url=');
```

### Proceso de empaquetado

El proyecto hermano (Electron) se encarga de:
1. Empaquetar PHP y PostgreSQL portables
2. Iniciar servidor PHP en puerto 8000
3. Abrir ventana de Electron con la aplicación
4. Gestionar el ciclo de vida de la aplicación

Ver el README del proyecto Electron para más detalles.

## 🔮 Desarrollo Futuro

### Planeado para próximas versiones

- [ ] **Integración con API de TCG**
  - Importación automática de precios
  - Actualización de datos de cartas
  - Validación de nombres y ediciones

- [ ] **Sistema de ventas completo**
  - Punto de venta (POS)
  - Generación de tickets
  - Reportes de ventas

- [ ] **Gestión de usuarios**
  - Múltiples usuarios con roles
  - Permisos granulares
  - Auditoría de acciones

- [ ] **Mejoras de UI/UX**
  - Implementación de framework CSS
  - Dashboard con estadísticas
  - Gráficos de ventas y stock

- [ ] **Optimizaciones**
  - Migración a variables de entorno (.env)
  - Sistema de caché
  - Paginación en listados
  - Búsqueda avanzada con filtros

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👥 Autores

**Teejosh Team**

## 📞 Soporte

Para reportar bugs o solicitar nuevas características, por favor abre un issue en el repositorio.

---

**Nota:** Este README será actualizado conforme el proyecto evolucione. Última actualización: Diciembre 2024.