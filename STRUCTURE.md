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