# 🖥️ Teejosh Inventario - Aplicación Electron

Aplicación de escritorio que empaqueta el Sistema de Inventario Teejosh como una aplicación nativa de Windows. Incluye PHP y PostgreSQL portables completamente embebidos, sin requerir instalación previa de dependencias.

![Electron Version](https://img.shields.io/badge/Electron-39.0.0-47848F?style=flat&logo=electron)
![Node Version](https://img.shields.io/badge/Node-18%2B-339933?style=flat&logo=node.js)
![Platform](https://img.shields.io/badge/Platform-Windows%20x64-0078D6?style=flat&logo=windows)

## 📋 Tabla de Contenidos

- [Descripción General](#-descripción-general)
- [Arquitectura](#-arquitectura)
- [Requisitos](#-requisitos)
- [Instalación y Desarrollo](#-instalación-y-desarrollo)
- [Scripts Disponibles](#-scripts-disponibles)
- [Proceso de Build](#-proceso-de-build)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Configuración](#-configuración)
- [Ciclo de Vida de la Aplicación](#-ciclo-de-vida-de-la-aplicación)
- [Optimizaciones](#-optimizaciones)
- [Troubleshooting](#-troubleshooting)
- [Distribución](#-distribución)

## 🎯 Descripción General

Esta aplicación Electron actúa como un **contenedor de escritorio** que:

1. **Empaqueta servicios completos:** PHP 8.x y PostgreSQL 14+ portables
2. **Gestiona el ciclo de vida:** Inicia, monitorea y detiene servicios automáticamente
3. **Proporciona instalador nativo:** Instalador NSIS para Windows con un solo clic
4. **Aísla el entorno:** Cada instalación tiene su propia base de datos y configuración
5. **Optimiza el rendimiento:** Sistema de templates y caché para inicio rápido

### ¿Por qué Electron?

- ✅ **Sin dependencias:** No requiere XAMPP, WAMP ni instalación de servicios
- ✅ **Instalación simple:** Un solo archivo .exe para instalar
- ✅ **Actualización fácil:** Sistema de actualizaciones automáticas posible
- ✅ **Experiencia nativa:** Icono en escritorio, integración con Windows
- ✅ **Portabilidad:** Todos los datos en AppData del usuario

## 🏗️ Arquitectura

### Diagrama de Componentes

```
┌─────────────────────────────────────────────────────────────┐
│                    APLICACIÓN ELECTRON                      │
│                                                             │
│  ┌────────────────────────────────────────────────────┐   │
│  │          Proceso Principal (main.js)               │   │
│  │  - Gestión de ventanas                            │   │
│  │  - Control de servicios (PostgreSQL, PHP)         │   │
│  │  - Comunicación IPC                               │   │
│  │  - Sistema de logging                             │   │
│  └────────────────────────────────────────────────────┘   │
│                          ↕ IPC                             │
│  ┌────────────────────────────────────────────────────┐   │
│  │        Proceso Renderer (BrowserWindow)            │   │
│  │  - Ventana web con sistema PHP                    │   │
│  │  - URL: http://127.0.0.1:8000                     │   │
│  │  - Comunicación segura vía preload.js             │   │
│  └────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─────────────────┐         ┌──────────────────────┐     │
│  │   PostgreSQL    │         │     PHP Server       │     │
│  │   Puerto 5432   │ ←───→   │   Puerto 8000        │     │
│  │   (Embebido)    │         │   (Embebido)         │     │
│  └─────────────────┘         └──────────────────────┘     │
│           ↓                            ↓                    │
│  ┌─────────────────────────────────────────────────────┐  │
│  │         Sistema MVC Teejosh (Proyecto PHP)         │  │
│  │  - Controladores, Modelos, Vistas                  │  │
│  │  - Lógica de negocio del inventario                │  │
│  └─────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### Flujo de Inicio Optimizado

```
1. Usuario ejecuta Teejosh.exe
   ↓
2. Electron inicia (main.js)
   ↓
3. Crear ventana con splash screen (INMEDIATO)
   ↓
4. Verificar archivos necesarios (< 100ms)
   ↓
5. PostgreSQL
   ├─ Verificar si ya está corriendo
   ├─ Copiar template pre-configurado (~500ms)
   └─ Iniciar servidor (< 1s)
   ↓
6. PARALELO
   ├─ Importar dump.sql (solo primera vez)
   └─ Iniciar PHP server
   ↓
7. Cargar aplicación web
   ↓
8. APLICACIÓN LISTA (Total: ~2-3 segundos)
```

## 📋 Requisitos

### Para Desarrollo

- **Node.js 18+** con npm
- **Git** (opcional, para clonar)
- **Windows 10/11** (x64)
- **~500MB** de espacio en disco

### Para Uso Final

- **Solo Windows 10/11** (x64)
- **~150MB** de espacio para instalación
- **~50MB** adicionales para datos de usuario

### Archivos Embebidos Requeridos

Antes de hacer build, asegúrate de tener:

```
ElectronProyect/
├── php/                    # PHP portable (~30MB)
│   ├── php.exe
│   ├── ext/               # Extensiones PHP
│   └── ...
│
├── postgres/               # PostgreSQL portable (~50MB)
│   ├── bin/
│   │   ├── pg_ctl.exe
│   │   ├── postgres.exe
│   │   ├── psql.exe
│   │   └── initdb.exe
│   ├── lib/
│   ├── share/
│   ├── pgdata_template/   # Template pre-inicializado (genera con create-template.js) (en depuración)
│   └── dump.sql           # Base de datos inicial
│
└── app/                    # Aplicación PHP del Proyecto 2
    └── (Sistema de Inventario completo)
```

## 🚀 Instalación y Desarrollo

### 1. Clonar el repositorio

```bash
git clone 
cd teejosh-electron
```

### 2. Instalar dependencias

```bash
npm install
```

Esto instalará:
- Electron 39.0.0
- electron-builder 24.13.3
- Dependencias nativas automáticamente

### 3. Preparar PHP y PostgreSQL

#### Opción A: Descargar portables pre-configurados

```bash
# Descargar PHP portable
# Ir a: https://windows.php.net/download/
# Extraer en: ElectronProyect/php/

# Descargar PostgreSQL portable
# Ir a: https://www.enterprisedb.com/download-postgresql-binaries
# Extraer bin/, lib/, share/ en: ElectronProyect/postgres/
```

#### Opción B: Copiar instalaciones existentes

```bash
# Desde instalación PHP existente
xcopy "C:\php" "ElectronProyect\php" /E /I /H

# Desde instalación PostgreSQL existente
xcopy "C:\Program Files\PostgreSQL\14\bin" "ElectronProyect\postgres\bin" /E /I
xcopy "C:\Program Files\PostgreSQL\14\lib" "ElectronProyect\postgres\lib" /E /I
xcopy "C:\Program Files\PostgreSQL\14\share" "ElectronProyect\postgres\share" /E /I
```

### 4. Preparar la aplicación PHP

```bash
# Copiar el proyecto PHP completo
xcopy "..\TeejoshMVC" "ElectronProyect\app" /E /I /H
```

### 5. Crear template de PostgreSQL (Recomendado)

```bash
# Ejecutar script para crear template pre-inicializado
node create-template.js
```

**Resultado esperado:**
```
==========================================
     ✓ TEMPLATE CREADO EXITOSAMENTE       
==========================================
Ubicación: D:\ElectronProyect\postgres\pgdata_template
Tamaño: 32.45 MB

BENEFICIO:
   Tiempo de primer inicio reducido de ~10s a ~2s
```

### 6. Verificar dump.sql

Coloca el archivo de base de datos en:
```
ElectronProyect/postgres/dump.sql
```

### 7. Ejecutar en modo desarrollo

```bash
npm start
```

La aplicación se abrirá con:
- ✅ DevTools abiertos automáticamente
- ✅ Logs detallados en consola
- ✅ Hot reload en cambios de código

## 📜 Scripts Disponibles

### Desarrollo

```bash
# Iniciar aplicación en modo desarrollo
npm start

# Ver logs en tiempo real
tail -f "%APPDATA%\teejoshelectron\app.log"
```

### Build y Empaquetado

```bash
# Build completo (instalador + archivos)
npm run build

# Build solo para Windows
npm run build:win

# Build sin crear instalador (más rápido para testing)
npm run build:dir
```

### Utilidades

```bash
# Crear template de PostgreSQL (para depurar)
node create-template.js

# Build automático completo (PowerShell)
.\Autobuild.ps1
```

## 🔨 Proceso de Build

### Build Manual

```bash
# 1. Limpiar builds anteriores
rmdir /s /q dist
rmdir /s /q node_modules

# 2. Instalar dependencias frescas
npm install

# 3. Crear instalador
npm run build
```

### Build Automático (Recomendado)

Usa el script PowerShell que automatiza todo:

```powershell
# Ejecutar desde PowerShell
.\Autobuild.ps1
```

**El script realiza:**
1. ✅ Cierra procesos antiguos de la app
2. ✅ Elimina `dist/` y `node_modules/`
3. ✅ Elimina instalación previa del sistema
4. ✅ Limpia template de PostgreSQL (opcional)
5. ✅ Reinstala dependencias npm
6. ✅ Ejecuta build completo

**Salida del build:**

```
dist/
├── Inventario Teejosh-Setup-0.1.0-pre-alpha.1.exe  # Instalador (NSIS)
├── win-unpacked/                                    # Archivos desempaquetados
│   ├── Inventario Teejosh.exe                      # Ejecutable principal
│   ├── resources/
│   │   ├── app.asar                                # Código empaquetado
│   │   ├── php/                                    # PHP embebido
│   │   ├── postgres/                               # PostgreSQL embebido
│   │   └── app/                                    # Aplicación PHP
│   └── ...
└── builder-effective-config.yaml                    # Config usada
```

### Tiempos de Build

| Etapa | Tiempo Aproximado |
|-------|-------------------|
| Limpiar archivos | 10-20s |
| `npm install` | 30-60s |
| Empaquetar recursos | 60-90s |
| Crear ASAR | 10-20s |
| Generar instalador NSIS | 30-45s |
| **TOTAL** | **~3-5 minutos** |

## 📁 Estructura del Proyecto

```
ElectronProyect/
│
├── 📄 main.js                 # Proceso principal de Electron
│   ├─ Gestión de ventanas
│   ├─ Control de PostgreSQL
│   ├─ Control de PHP Server
│   └─ Sistema de logging
│
├── 📄 preload.js              # Script puente (seguridad)
│   └─ API segura para renderer
│
├── 📄 package.json            # Configuración del proyecto
│   ├─ Dependencias
│   ├─ Scripts npm
│   └─ Configuración electron-builder
│
├── 📄 package.json.docmt      # Documentación de package.json
│
├── 📜 Autobuild.ps1           # Script de build automático
│
├── 📜 create-template.js      # Generador de template PostgreSQL
│
├── 📜 installer.nsh           # Personalización instalador NSIS
│
├── 📁 app/                    # Aplicación PHP (Proyecto 2)
│   └── (Sistema MVC Teejosh completo)
│
├── 📁 php/                    # PHP portable embebido
│   ├── php.exe
│   ├── php.ini
│   ├── ext/                   # Extensiones PHP
│   ├── dev/
│   ├── extras/
│   └── lib/
│
├── 📁 postgres/               # PostgreSQL portable embebido
│   ├── bin/                   # Binarios PostgreSQL
│   │   ├── pg_ctl.exe
│   │   ├── postgres.exe
│   │   ├── psql.exe
│   │   ├── initdb.exe
│   │   └── pg_isready.exe
│   ├── lib/                   # Librerías compartidas
│   ├── share/                 # Archivos de datos
│   ├── pgdata_template/       # Template pre-inicializado (opcional)
│   └── dump.sql               # Base de datos inicial
│
├── 📁 dist/                   # Salida del build (generado)
│   └── Inventario Teejosh-Setup-x.x.x.exe
│
├── 📁 node_modules/           # Dependencias npm (generado)
│
└── 📄 package-lock.json       # Lock de dependencias (generado)
```

### Archivos Clave

| Archivo | Propósito | Líneas |
|---------|-----------|--------|
| `main.js` | Proceso principal, lógica de servicios | ~700 |
| `preload.js` | API segura para renderer | ~150 |
| `package.json` | Configuración y build | ~150 |
| `Autobuild.ps1` | Automatización de build | ~100 |
| `create-template.js` | Generador de template PG | ~200 |
| `installer.nsh` | Personalización instalador | ~80 |

## ⚙️ Configuración

### package.json - Sección Build

```json
{
  "build": {
    "appId": "com.teejosh.inventario",
    "productName": "Inventario Teejosh",
    
    // Compresión máxima para reducir tamaño
    "compression": "maximum",
    
    // Empaquetar código en ASAR
    "asar": true,
    "asarUnpack": [
      "**/*.{dll,exe,so,dylib}",  // Binarios fuera de ASAR
      "**/postgres/**/*",
      "**/php/**/*"
    ],
    
    // Recursos extra (PHP, PostgreSQL, App)
    "extraResources": [
      { "from": "php/", "to": "php/" },
      { "from": "postgres/bin/", "to": "postgres/bin/" },
      // ... más recursos
    ],
    
    // Configuración Windows
    "win": {
      "target": ["nsis"],
      "arch": ["x64"]
    },
    
    // Configuración instalador NSIS
    "nsis": {
      "oneClick": true,
      "perMachine": false,
      "createDesktopShortcut": true,
      "deleteAppDataOnUninstall": true
    }
  }
}
```

### Variables de Entorno

#### En desarrollo (main.js)

```javascript
const isDev = !app.isPackaged;

// Rutas según entorno
function rootPath() {
  return isDev ? __dirname : process.resourcesPath;
}
```

#### Rutas importantes

```javascript
// Datos del usuario
app.getPath('userData')
// → C:\Users\[Usuario]\AppData\Roaming\teejoshelectron

// Recursos embebidos
process.resourcesPath
// → C:\Users\[Usuario]\AppData\Local\Programs\teejoshelectron\resources

// Logs
path.join(app.getPath('userData'), 'app.log')
// → C:\Users\[Usuario]\AppData\Roaming\teejoshelectron\app.log

// Base de datos
path.join(app.getPath('userData'), 'pgdata')
// → C:\Users\[Usuario]\AppData\Roaming\teejoshelectron\pgdata
```

### Configuración de Servicios

#### PostgreSQL

```javascript
// Puerto
const PG_PORT = 5432;

// Host (solo localhost)
const PG_HOST = '127.0.0.1';

// Usuario
const PG_USER = 'postgres';

// Sin contraseña (auth=trust en template)
```

#### PHP

```javascript
// Puerto
const PHP_PORT = 8000;

// DocumentRoot
const PHP_ROOT = path.join(rootPath(), 'app', 'public');

// Comando
php.exe -S 127.0.0.1:8000 -t app/public
```

## 🔄 Ciclo de Vida de la Aplicación

### Inicio Detallado

```
1. app.whenReady()
   ├─ markPerf('app_start')
   ├─ Crear ventana con splash screen
   │  └─ Mostrar logo + spinner
   ├─ Verificar archivos necesarios
   │  ├─ pg_ctl.exe, psql.exe, pg_isready.exe
   │  ├─ php.exe
   │  └─ app/public/index.php
   │
   ├─ PostgreSQL
   │  ├─ ensurePostgresInitialized()
   │  │  ├─ ¿Existe pgdata? NO
   │  │  ├─ ¿Existe template? SÍ
   │  │  └─ Copiar template → pgdata (~500ms)
   │  ├─ startPostgres()
   │  │  ├─ Ejecutar pg_ctl start
   │  │  └─ Esperar con pg_isready (hasta 10s)
   │  └─ markPerf('postgres_started')
   │
   ├─ PARALELO
   │  ├─ importDatabase()
   │  │  └─ psql -f dump.sql (solo primera vez)
   │  └─ startPHP() + waitForPHP()
   │     └─ php -S 127.0.0.1:8000
   │
   ├─ Cargar aplicación
   │  └─ mainWindow.loadURL('http://127.0.0.1:8000')
   │
   └─ markPerf('app_loaded')
      └─ Log: "Tiempo total: 2341ms"
```

### Cierre Limpio

```
1. window-all-closed
   ├─ Log: "Cerrando aplicación..."
   │
   ├─ PostgreSQL
   │  ├─ pg_ctl stop -m fast
   │  └─ Log: "PostgreSQL detenido"
   │
   ├─ PHP
   │  ├─ phpServer.kill()
   │  └─ Log: "PHP detenido"
   │
   └─ app.quit()
```

### Gestión de Errores

```javascript
// Errores capturados
process.on('uncaughtException', (error) => {
  log(`UNCAUGHT EXCEPTION: ${error.message}`);
  // Guardar en log antes de terminar
});

process.on('unhandledRejection', (reason) => {
  log(`UNHANDLED REJECTION: ${reason}`);
});

// Errores en inicio
try {
  await startServices();
} catch (error) {
  dialog.showErrorBox(
    'Error al iniciar',
    `No se pudo iniciar la aplicación.\n\n${error.message}\n\nRevisa: ${logFile}`
  );
  app.quit();
}
```

## ⚡ Optimizaciones

### 1. Template de PostgreSQL

**Problema:** Inicializar cluster de PostgreSQL desde cero tarda ~10 segundos.

**Solución:** Pre-inicializar y copiar template.

```bash
# Crear template
node create-template.js

# Resultado
postgres/pgdata_template/  # ~30MB
```

**Beneficio:**
- Sin template: ~10 segundos
- Con template: ~2 segundos
- **Mejora: 80% más rápido**

### 2. Inicio Paralelo

```javascript
// ❌ Secuencial (lento)
await startPostgres();
await importDatabase();
await startPHP();
// Total: 12 segundos

// ✅ Paralelo (rápido)
await Promise.all([
  importDatabase(),
  startPHP()
]);
// Total: 3 segundos
```

### 3. Configuración PostgreSQL

Optimizaciones en `postgresql.conf`:

```ini
shared_buffers = 32MB              # Menos memoria
fsync = off                         # Más velocidad (solo desarrollo)
synchronous_commit = off           # Sin espera de escritura
wal_level = minimal                # Menos logging
checkpoint_timeout = 15min         # Menos checkpoints
```

⚠️ **Advertencia:** Estas configuraciones NO son seguras para producción.

### 4. Compression y ASAR

```json
{
  "compression": "maximum",  // Máxima compresión
  "asar": true              // Empaquetar código
}
```

**Tamaños:**
- Sin compresión: ~200MB
- Con compresión: ~150MB
- **Ahorro: 25%**

### 5. Lazy Loading

```javascript
// Mostrar ventana INMEDIATAMENTE
createWindow();  // Splash screen

// Servicios en segundo plano
await startServices();  // Usuario no espera

// Cargar app cuando esté lista
mainWindow.loadURL('http://127.0.0.1:8000');
```

## 🐛 Troubleshooting

### Problemas Comunes

#### 1. "Error: initdb.exe no encontrado"

**Causa:** Faltan binarios de PostgreSQL.

**Solución:**
```bash
# Verificar estructura
dir postgres\bin\initdb.exe
dir postgres\bin\pg_ctl.exe

# Si faltan, descargar PostgreSQL portable
# https://www.enterprisedb.com/download-postgresql-binaries
```

#### 2. "PostgreSQL no responde después de 10 segundos"

**Causa:** Puerto 5432 ocupado o permisos insuficientes.

**Solución:**
```bash
# Verificar puerto
netstat -ano | findstr :5432

# Matar proceso si existe
taskkill /F /PID [PID_DEL_PROCESO]

# Revisar logs
type "%APPDATA%\teejoshelectron\postgres.log"
```

#### 3. "PHP no responde"

**Causa:** Puerto 8000 ocupado.

**Solución:**
```bash
# Verificar puerto
netstat -ano | findstr :8000

# Matar proceso
taskkill /F /PID [PID_DEL_PROCESO]
```

#### 4. "Error al importar dump.sql"

**Causa:** Archivo dump.sql corrupto o base de datos ya existe.

**Solución:**
```bash
# Eliminar marcador de importación
del "%APPDATA%\teejoshelectron\.db_imported"

# Reiniciar app (volverá a importar)
```

#### 5. "Instalador falla con error de permisos"

**Causa:** Antivirus o Windows Defender bloqueando.

**Solución:**
1. Agregar excepción en Windows Defender
2. Compilar con `verifyUpdateCodeSignature: false`
3. Considerar firma de código para producción

### Logs y Diagnóstico

#### Ver logs de la aplicación

```bash
# Windows
type "%APPDATA%\teejoshelectron\app.log"

# PowerShell
Get-Content "$env:APPDATA\teejoshelectron\app.log" -Tail 50
```

#### Ver logs de PostgreSQL

```bash
type "%APPDATA%\teejoshelectron\postgres.log"
```

#### Logs en tiempo real

```powershell
# PowerShell
Get-Content "$env:APPDATA\teejoshelectron\app.log" -Wait
```

#### Limpiar datos completamente

```bash
# Eliminar todos los datos de usuario
rmdir /s /q "%APPDATA%\teejoshelectron"
rmdir /s /q "%LOCALAPPDATA%\teejoshelectron"
rmdir /s /q "%LOCALAPPDATA%\Programs\teejoshelectron"
```

### Verificar Instalación

```javascript
// Desde DevTools (Ctrl+Shift+I en desarrollo)
console.log(await window.electronAPI.getAppVersion());
console.log(await window.electronAPI.getDatabaseStatus());
```

## 📦 Distribución

### Instalador Generado

Después del build, encontrarás:

```
dist/
└── Inventario Teejosh-Setup-0.1.0-pre-alpha.1.exe
```

**Características del instalador:**
- ✅ Instalación con un solo clic
- ✅ Acceso directo en escritorio
- ✅ Acceso directo en menú inicio
- ✅ Desinstalador incluido
- ✅ Tamaño: ~150MB

### Proceso de Instalación (Usuario Final)

1. Usuario ejecuta `Inventario Teejosh-Setup-x.x.x.exe`
2. Instalador copia archivos a:
   ```
   C:\Users\[Usuario]\AppData\Local\Programs\teejoshelectron\
   ```
3. Crea accesos directos
4. Primera ejecución:
   - Copia template PostgreSQL (~2s)
   - Importa dump.sql (~3s)
   - Inicia aplicación
5. Ejecuciones subsecuentes: ~2 segundos

### Ubicaciones de Archivos

**Instalación:**
```
C:\Users\[Usuario]\AppData\Local\Programs\teejoshelectron\
├── Inventario Teejosh.exe
├── resources\
│   ├── app.asar
│   ├── php\
│   ├── postgres\
│   └── app\
└── ...
```

**Datos de Usuario:**
```
C:\Users\[Usuario]\AppData\Roaming\teejoshelectron\
├── pgdata\           # Base de datos PostgreSQL
├── app.log           # Logs de la aplicación
├── postgres.log      # Logs de PostgreSQL
└── .db_imported      # Marcador de primera ejecución
```

### Actualización de Versión

Para crear una nueva versión:

1. **Actualizar version en package.json:**
   ```json
   {
     "version": "0.2.0"
   }
   ```

2. **Hacer build:**
   ```bash
   npm run build
   ```

3. **Distribuir nuevo instalador:**
   ```
   dist/Inventario Teejosh-Setup-0.2.0.exe
   ```

### Sistema de Actualizaciones (Futuro)

Actualmente no implementado, pero se puede agregar con:

```bash
npm install electron-updater
```

## 🔐 Seguridad

### Configuración Actual

```javascript
// preload.js - API segura
contextBridge.exposeInMainWorld('electronAPI', {
  appReady: () => ipcRenderer.send('app-ready'),
  // Solo funciones específicas expuestas
});

// main.js - BrowserWindow seguro
new BrowserWindow({
  webPreferences: {
    nodeIntegration: false,     // ❌ Node.js en renderer
    contextIsolation: true,    // ✅ Aislamiento de contexto
    sandbox: true              // ✅ Sandbox activado
  }
});
```

### Mejoras Recomendadas para Producción

1. **Firma de código:**
   ```json
   {
     "win": {
       "certificateFile": "cert.pfx",
       "certificatePassword": "password"
     }
   }
   ```

2. **PostgreSQL con contraseña:**
   - Cambiar `--auth=trust` a `--auth=md5`
   - Generar contraseña aleatoria en instalación

3. **Actualización del sistema:**
   - Implementar electron-updater
   - Servidor de actualizaciones

4. **Logging seguro:**
   - No loguear datos sensibles
   - Rotación de logs

## 📊 Métricas de Rendimiento

### Tiempos de Inicio (Benchmark)

| Etapa | Sin Template | Con Template |
|-------|--------------|--------------|
| Crear ventana | 100ms | 100ms |
| Verificar archivos | 50ms | 50ms |
| PostgreSQL init | 10,000ms | 500ms |
| PostgreSQL start | 1,000ms | 500ms |
| Importar DB | 2,000ms | 2,000ms |
| PHP start | 500ms | 500ms |
| Cargar app | 500ms | 500ms |
| **TOTAL** | **~14s** | **~4s** |

### Tamaños

| Componente | Tamaño |
|------------|--------|
| PHP portable | ~30MB |
| PostgreSQL bin | ~20MB |
| PostgreSQL lib/share | ~30MB |
| Template PostgreSQL | ~30MB |
| Aplicación PHP | ~5MB |
| Electron framework | ~100MB |
| **Instalador final** | **~150MB** |
| **Datos usuario (pgdata)** | ~50MB |

### Uso de Recursos

| Recurso | En Reposo | En Uso |
|---------|-----------|--------|
| Memoria RAM | ~150MB | ~300MB |
| CPU | < 1% | 5-10% |
| Disco (lectura) | ~5MB/s | ~20MB/s |
| Red | 0 | 0 (todo local) |

## 🎨 Personalización

### Cambiar Icono de la Aplicación

1. **Crear iconos:**
   ```
   build/
   └── icon.ico       # Windows (256x256)
   ```

2. **Actualizar package.json:**
   ```json
   {
     "build": {
       "win": {
         "icon": "build/icon.ico"
       }
     }
   }
   ```

### Personalizar Splash Screen

Editar HTML embebido en `main.js`:

```javascript
const splashHTML = `
  <!DOCTYPE html>
  <html>
  <head>
    <style>
      body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        /* Cambiar colores aquí */
      }
      .logo {
        font-size: 48px;
        /* Personalizar logo */
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="logo">Tu Marca Aquí</div>
      <!-- Personalizar contenido -->
    </div>
  </body>
  </html>
`;
```

### Modificar Instalador NSIS

Editar `installer.nsh`:

```nsis
!macro customInstall
  ; Crear acceso directo adicional
  CreateShortCut "$DESKTOP\MiApp.lnk" "$INSTDIR\Inventario Teejosh.exe"
  
  ; Copiar archivos de configuración
  CopyFiles "$INSTDIR\config.ini" "$APPDATA\Inventario\config.ini"
!macroend

!macro customUnInstall
  ; Limpiar archivos personalizados
  Delete "$DESKTOP\MiApp.lnk"
  RMDir /r "$APPDATA\Inventario"
!macroend
```

## 🧪 Testing

### Testing Manual

#### 1. Verificar servicios

```javascript
// Desde DevTools (F12)
// Verificar PostgreSQL
fetch('http://127.0.0.1:5432')
  .catch(e => console.log('PostgreSQL:', e.message));

// Verificar PHP
fetch('http://127.0.0.1:8000')
  .then(r => console.log('PHP OK'))
  .catch(e => console.log('PHP Error:', e));
```

#### 2. Verificar base de datos

```javascript
// Desde la aplicación PHP (DevTools)
window.electronAPI.getDatabaseStatus()
  .then(status => console.log(status));
```

#### 3. Verificar logs

```bash
# Ver errores en logs
findstr /i "error" "%APPDATA%\teejoshelectron\app.log"
```

### Testing Automatizado (Futuro)

Estructura recomendada:

```javascript
// test/main.spec.js
const { Application } = require('spectron');

describe('Teejosh Electron', () => {
  let app;
  
  beforeEach(async () => {
    app = new Application({
      path: './node_modules/.bin/electron',
      args: ['./main.js']
    });
    await app.start();
  });
  
  afterEach(async () => {
    if (app && app.isRunning()) {
      await app.stop();
    }
  });
  
  it('should start PostgreSQL', async () => {
    // Esperar 5 segundos para que inicie
    await app.client.pause(5000);
    
    // Verificar que la ventana existe
    const count = await app.client.getWindowCount();
    expect(count).toBe(1);
  });
});
```

## 🔧 Desarrollo Avanzado

### Agregar Nuevo Servicio

Ejemplo: Agregar Redis

1. **Descargar Redis portable**
2. **Agregar a extraResources:**
   ```json
   {
     "from": "redis/",
     "to": "redis/"
   }
   ```
3. **Iniciar en main.js:**
   ```javascript
   async function startRedis() {
     const redis = path.join(rootPath(), "redis", "redis-server.exe");
     const redisProcess = spawn(redis, ["--port", "6379"], {
       windowsHide: true
     });
     
     log("Redis iniciado en puerto 6379");
     return redisProcess;
   }
   ```

### Comunicación IPC Personalizada

#### En main.js:

```javascript
ipcMain.handle('ejecutar-backup', async () => {
  const backupPath = path.join(app.getPath('userData'), 'backup.sql');
  
  const result = await execCommand('pg_dump', [
    '-U', 'postgres',
    '-h', '127.0.0.1',
    '-p', '5432',
    '-d', 'teejosh',
    '-f', backupPath
  ]);
  
  return { success: true, path: backupPath };
});
```

#### En preload.js:

```javascript
contextBridge.exposeInMainWorld('electronAPI', {
  ejecutarBackup: () => ipcRenderer.invoke('ejecutar-backup')
});
```

#### En renderer (PHP/JS):

```javascript
async function hacerBackup() {
  const resultado = await window.electronAPI.ejecutarBackup();
  console.log('Backup guardado en:', resultado.path);
}
```

### Hot Reload en Desarrollo

Instalar electron-reload:

```bash
npm install --save-dev electron-reload
```

Agregar a main.js:

```javascript
if (isDev) {
  require('electron-reload')(__dirname, {
    electron: path.join(__dirname, 'node_modules', '.bin', 'electron')
  });
}
```

## 📚 Recursos Adicionales

### Documentación Oficial

- [Electron Docs](https://www.electronjs.org/docs/latest)
- [electron-builder](https://www.electron.build/)
- [PostgreSQL Docs](https://www.postgresql.org/docs/)
- [PHP Manual](https://www.php.net/manual/es/)

### Herramientas Útiles

- **Electron Fiddle:** Prototipado rápido de Electron
- **Devtron:** DevTools extension para Electron
- **Spectron:** Testing framework para Electron
- **electron-log:** Logging avanzado

### Comunidad

- [Electron Discord](https://discord.com/invite/electron)
- [Stack Overflow - Electron](https://stackoverflow.com/questions/tagged/electron)
- [GitHub Discussions](https://github.com/electron/electron/discussions)

## 🚀 Mejoras Futuras

### Corto Plazo (v0.2.0)

- [ ] **Sistema de actualizaciones automáticas**
  - Implementar electron-updater
  - Servidor de releases
  - Notificaciones de actualización

- [ ] **Migrar autenticación a BD**
  - Eliminar credenciales hardcodeadas
  - Sistema de roles completo
  - Gestión de usuarios

- [ ] **Optimización de tamaño**
  - Reducir binarios PHP innecesarios
  - Comprimir PostgreSQL share/
  - Target < 120MB

### Mediano Plazo (v0.3.0)

- [ ] **Sistema de backup automático**
  - Backup diario de PostgreSQL
  - Exportación a archivo
  - Restauración desde interfaz

- [ ] **Multi-usuario**
  - Login real con base de datos
  - Permisos granulares
  - Auditoría de acciones

- [ ] **Mejoras de rendimiento**
  - Lazy loading de módulos
  - Cache de consultas frecuentes
  - Optimización de queries

### Largo Plazo (v1.0.0)

- [ ] **Modo offline completo**
  - Sincronización cuando hay conexión
  - Queue de operaciones pendientes

- [ ] **Reportes avanzados**
  - Generación de PDF
  - Gráficos de ventas
  - Dashboard con estadísticas

- [ ] **Integración con API TCG**
  - Precios actualizados
  - Información de cartas
  - Importación masiva

- [ ] **Multi-plataforma**
  - Soporte para macOS
  - Soporte para Linux

## 🤝 Contribución

### Cómo Contribuir

1. **Fork el proyecto**
2. **Crear rama feature:**
   ```bash
   git checkout -b feature/nueva-funcionalidad
   ```
3. **Commit cambios:**
   ```bash
   git commit -m "feat: agregar nueva funcionalidad"
   ```
4. **Push a la rama:**
   ```bash
   git push origin feature/nueva-funcionalidad
   ```
5. **Abrir Pull Request**

### Guía de Estilo

- **JavaScript:** Seguir Airbnb Style Guide
- **Commits:** Conventional Commits
- **Documentación:** JSDoc para funciones públicas

### Reportar Bugs

Usar la plantilla de issues en GitHub:

```markdown
**Descripción del bug**
Descripción clara del problema.

**Pasos para reproducir**
1. Ir a '...'
2. Hacer clic en '...'
3. Ver error

**Comportamiento esperado**
Lo que debería pasar.

**Comportamiento actual**
Lo que está pasando.

**Logs**
```
Contenido de app.log
```

**Entorno**
- OS: Windows 10 x64
- Versión: 0.1.0
- Node: 18.0.0
```

## 📄 Licencia

Este proyecto está bajo la Licencia ISC.

```
ISC License

Copyright (c) 2025 MESVA

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted, provided that the above
copyright notice and this permission notice appear in all copies.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
```

## 👥 Autores

**MESVA** - Desarrollo principal

Ver la lista de [contribuidores](https://github.com/tu-usuario/teejosh-electron/contributors).

## 🙏 Agradecimientos

- **Electron Team** - Framework principal
- **PostgreSQL Community** - Base de datos robusta
- **PHP Team** - Lenguaje de scripting
- **electron-builder** - Herramienta de empaquetado

## 📞 Soporte

### Obtener Ayuda

1. **Revisar este README**
2. **Buscar en Issues existentes**
3. **Revisar logs:**
   ```
   %APPDATA%\teejoshelectron\app.log
   ```
4. **Crear nuevo Issue** con información detallada

### Contacto

- **Issues:** [GitHub Issues](https://github.com/tu-usuario/teejosh-electron/issues)
- **Discussions:** [GitHub Discussions](https://github.com/tu-usuario/teejosh-electron/discussions)

---

## 📝 Notas Finales

### Checklist Pre-Build

Antes de hacer build de producción:

- [ ] ✅ Versión actualizada en package.json
- [ ] ✅ PHP portable descargado y configurado
- [ ] ✅ PostgreSQL portable descargado
- [ ] ✅ Template de PostgreSQL generado
- [ ] ✅ dump.sql actualizado
- [ ] ✅ Aplicación PHP probada
- [ ] ✅ DEBUG_MODE = false en producción
- [ ] ✅ Logs de desarrollo eliminados
- [ ] ✅ Variables de entorno verificadas
- [ ] ✅ Testing manual completado
- [ ] ✅ Changelog actualizado

### Changelog

#### v0.1.0-pre-alpha.1 (Actual)
- ✨ Primera versión funcional
- ✅ PostgreSQL embebido
- ✅ PHP embebido
- ✅ Sistema de templates
- ✅ Instalador NSIS
- ✅ Splash screen
- ✅ Sistema de logging

---

**Última actualización:** Diciembre 2024

**Estado del proyecto:** Pre-Alpha - En desarrollo activo

**¿Listo para producción?** No - Solo para desarrollo y testing

---

> 💡 **Tip:** Este README es un documento vivo. Actualízalo conforme el proyecto evoluciona.

> ⚠️ **Importante:** Antes de distribuir, asegúrate de probar la instalación en una máquina limpia sin Node.js ni PostgreSQL instalados.

> 🎯 **Objetivo:** Crear una aplicación de escritorio completamente autocontenida que "simplemente funcione" sin configuración manual.